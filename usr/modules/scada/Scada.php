<?php

/*
* @name        JMY CORE
* @link        https://jmy.su/
* @copyright   Copyright (C) 2012-2021 JMY LTD
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
* @description Scada Public Controller
*/

namespace Scada;

use \Boot\Traits\Key;
use \Boot\Support\Numb;
use Boot\Support\Carbon;
use Scada\Models\ScadaTags;
use Scada\Models\ScadaValues;
use \Scada\Models\ScadaActions;
use ModbusTcpClient\Utils\Types;
use ModbusTcpClient\Utils\Endian;
use \Scada\Traits\RegisterResponse;
use Scada\Models\Scada as ScadaModel;
use Boot\Controller\AbstractController;
use ModbusTcpClient\Packet\RtuConverter;
use ModbusTcpClient\Packet\ResponseFactory;
use ModbusTcpClient\Network\BinaryStreamConnection;
use ModbusTcpClient\Packet\ModbusFunction\ReadCoilsRequest;
use ModbusTcpClient\Packet\ModbusFunction\WriteSingleCoilRequest;
use ModbusTcpClient\Packet\ModbusFunction\WriteSingleRegisterRequest;
use ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersRequest;
use ModbusTcpClient\Packet\ModbusFunction\WriteMultipleRegistersRequest;
use ModbusTcpClient\Packet\ModbusFunction\ReadWriteMultipleRegistersRequest;

class Scada extends AbstractController
{
  use Key, RegisterResponse;

  static $key = ROOT . '/tmp/key/scada.daemon.key';

  static $debug = true;

  /**
   * checkScadaConnect
   *
   * @return void
   */
  public static function checkScadaConnect()
  {
  }

  /**
   * prepareRegister
   *
   * @param  mixed $scadaTags
   * @return array
   */
  public static function prepareRegister($scadaTags)
  {
    $result = [];
    foreach ($scadaTags as $value) {
      if (in_array($value->registr, ['ReadInputDiscretesRequest', 'ReadInputRegistersRequest', 'ReadHoldingRegistersRequest', 'ReadWriteMultipleRegistersRequest', 'ReadWriteSingleCoilRequest'])) {
        $address = $value->address;
        if (!is_null($value->byte)) {
          $address .= '.' . $value->byte;
        }
        if (!isset($result[$value->unit_id][$value->registr])) {
          $result[$value->unit_id][$value->registr]['startAddress'] = $value->address;
          $result[$value->unit_id][$value->registr]['quantity'] = $value->quantity;
          $result[$value->unit_id][$value->registr]['tags'][$address] = $value;
        } else {
          if ($result[$value->unit_id][$value->registr]['startAddress'] > $value->address) {
            $result[$value->unit_id][$value->registr]['startAddress'] = $value->address;
          }
          if ($result[$value->unit_id][$value->registr]['quantity'] < $value->address) {
            $result[$value->unit_id][$value->registr]['quantity'] = $value->address;
          }
          $result[$value->unit_id][$value->registr]['tags'][$address] = $value;
        }
      }
    }
    return $result;
  }

  public static $modbusFunction = [
    "ReadInputRegistersRequest" => '\ModbusTcpClient\Packet\ModbusFunction\ReadInputRegistersRequest',
    "ReadInputDiscretesRequest" => '\ModbusTcpClient\Packet\ModbusFunction\ReadInputDiscretesRequest',
    "ReadHoldingRegistersRequest" => '\ModbusTcpClient\Packet\ModbusFunction\ReadHoldingRegistersRequest',
  ];

  public static function collectScada($id)
  {
    $now = Carbon::now()->toDateTimeString();
    static::d('Начало опроса');
    foreach (ScadaModel::where('id', $id)->get() as $sc) {
      try {
        $tags = static::getScada($sc->id);
      } catch (\Exception $exception) {
        dump('An exception occurred' . PHP_EOL);
        dump($exception->getMessage() . PHP_EOL);
        dump($exception->getTraceAsString() . PHP_EOL);
      }
      foreach ($tags as $tag) {
        $request[] = [
          'id' => $tag['id'],
          'tag_id' => $tag['id'],
          'field_id' => $tag['field_id'],
          'field_type' => $tag['field_type'],
          'type' => $tag['type'],
          'title' => $tag['title'],
          'scada_id' => $sc->id,
          'value' =>  $tag['value'],
          'created_at' => $now,
          'updated_at' => $now,
        ];
        ScadaModel::find($sc->id)->update(['polling_at' => $now]);
        ScadaTags::where('id', $tag['id'])->limit(1)->update(['last_value' => $tag['value'], 'polling_at' => $now]);
        $sv = ScadaValues::where('scada_tags_id', $tag['id'])->orderBy('id', 'DESC')->first();
        if (empty($sv) || $sv->value != $tag['value']) {
          ScadaValues::create([
            'scada_tags_id' => $tag['id'],
            'value' => $tag['value'],
          ]);
        }
      }
      if (!empty($request)) {
        dump($request);
      }
    }
    static::d('Опрос окончен');
  }

  public static function getScadaCollect($id)
  {

    foreach (ScadaModel::where('status', 1)->get() as $key => $sc) {
      $request = [];
      $scadaTags = ScadaTags::where('scada_id', $sc->id)->where('status', 1)->get();
      if (!empty($scadaTags)) {
        foreach ($scadaTags as $value) {
          // $currentValue = $sv->firstWhere('tag_id', $value->id);
          $request[] = [
            'id' => $value->id,
            'tag_id' => $value->id,
            'actions' => $value->actions,
            'field_id' => $value->field_id,
            'field_type' => $value->field_type,
            'type' => $value->type,
            'title' => $value->title,
            'scada_id' => $value->scada_id,
            'value' =>   $value->last_value ?? null,
            'created_at' => $value->created_at,
            'updated_at' =>  !empty($value->updated_at) ? date('d.m.Y H:i:s', strtotime($value->updated_at)) : null,
          ];
        }
      }
      try {
        $localsocket = 'tcp://atri-energo.ru:1234';
        $fp = stream_socket_client($localsocket, $errno, $errstr, 3);
        if (!$fp) {
          throw new \Exception("$errstr ($errno)", 1);
        } else {
          fwrite($fp, json_encode(['message_type' => 'scada', 'scada_id' => $sc->id, 'message' => $request])  . "\n");
          fclose($fp);
        }
      } catch (\Exception $th) {
        dump('Не могу отправить в WebSocket');
      }
    }
  }

  protected static function d($message = null)
  {
    if (static::$debug) {
      dump($message);
    }
  }

  /**
   * getScada
   *
   * @param  mixed $id
   * @return array
   */
  public static function getScada($id)
  {
    if (isset($id)) {
      static::d('Объект №' . $id);
      $scada = ScadaModel::find($id);
      if (!empty($scada)) {
        $scadaTags = ScadaTags::where('scada_id', $id)->where('status', 1)->get();
        $rtuConverter = $scada->rtuConverter;
        if (!empty($scadaTags)) {
          static::d('Тэги получены');
          $register = static::prepareRegister($scadaTags);
          if (!empty($register)) {
            $connection = BinaryStreamConnection::getBuilder()
              ->setPort($scada->port)
              ->setHost($scada->ip)
              ->setReadTimeoutSec(15)
              ->setTimeoutSec(15)
              ->setConnectTimeoutSec(15)
              ->setWriteTimeoutSec(15)
              ->setIsCompleteCallback(function ($binaryData, $streamIndex) {
                return true;
              })
              ->build()->connect();
            Endian::$defaultEndian = Endian::BIG_ENDIAN_LOW_WORD_FIRST;

            $resultTags = [];

            foreach ($register as $slaveId => $reg) {
              foreach ($reg as $k => $v) {

                $method = 'get' . $k;
                $scadaActions = cc();
                if (static::key_check()) {
                  static::d('Есть действия на запись');
                  $scadaActions = ScadaActions::where('status', 0)->get();
                }
                static::d('Чтение регистра ' . $k . ' на устройстве ' . $slaveId);
                switch ($k) {
                  case 'ReadWriteMultipleRegistersRequest':
                    if (isset($v['tags'])) {
                      foreach ($v['tags'] as $keyTag => $valueTag) {
                        $sa = $scadaActions->firstWhere('tag_id', $valueTag->id);
                        if (!empty($sa)) {
                          $newValue = Numb::prepareNumeric($sa->value);
                          // dd( $valueTag->coefficient_a);
                          if ($valueTag->recalculation == 1) {
                            $newValue = $newValue / $valueTag->coefficient_a - $valueTag->coefficient_b;
                          }
                          switch ($valueTag->endian) {
                            case 'BIG_ENDIAN':
                              $endian = 0 | 1;
                              break;

                            default:
                              $endian = null;
                              break;
                          }
                          if ($valueTag->type == 'float') {
                            $currentValue = Types::toReal($newValue, $endian);
                          } else {
                            $currentValue = Types::toInt16($newValue, $endian);
                          }

                          // dd( $currentValue);

                          $writeRegisters[] = $currentValue;

                          $packet = new ReadWriteMultipleRegistersRequest(
                            $valueTag->address,
                            $valueTag->quantity,
                            $valueTag->address,
                            $writeRegisters,
                            $slaveId
                          );
                          // dump($valueTag->address, $writeRegisters);
                          $packet = new WriteMultipleRegistersRequest($valueTag->address, $writeRegisters, $slaveId);
                          // $packet = new WriteSingleRegisterRequest($valueTag->address, $newValue, $slaveId);
                          static::sendAndReceive($connection, $packet, $rtuConverter);
                          $packet = new ReadHoldingRegistersRequest($valueTag->address, $valueTag->quantity, $slaveId);
                          // exit;
                          // dd($packet);
                          ScadaActions::find($sa->id)->update(['status' => '1']);
                        } else {
                          $packet = new ReadHoldingRegistersRequest($valueTag->address, $valueTag->quantity, $slaveId);
                        }
                        $tags = [];
                        $tags[$keyTag] = $valueTag;
                        $resultTags = array_merge($resultTags, static::$method(static::sendAndReceive($connection, $packet, $rtuConverter), $valueTag->address, $tags));
                      }
                    }
                    break;

                  case 'ReadWriteSingleCoilRequest':
                    if (isset($v['tags'])) {
                      foreach ($v['tags'] as $keyTag => $valueTag) {
                        $sa = $scadaActions->firstWhere('tag_id', $valueTag->id);
                        if (!empty($sa)) {
                          $startAddress = 1;
                          $coil = ($sa->value == 'true') ? true : false;
                          $packet = new WriteSingleCoilRequest($valueTag->address, $coil, $slaveId);
                          static::sendAndReceive($connection, $packet, $rtuConverter);
                          if ($valueTag->actions_type == 'CommandReset') {
                            sleep(2);
                            $packet = new WriteSingleCoilRequest($valueTag->address, !$coil, $slaveId);
                            static::sendAndReceive($connection, $packet, $rtuConverter);
                          }
                          ScadaActions::find($sa->id)->update(['status' => '1']);
                          sleep(2);
                        }
                        $packet = new ReadCoilsRequest($valueTag->address, $valueTag->quantity - 1, $slaveId);
                        $tags = [];
                        $tags[$keyTag] = $valueTag;
                        $resultTags = array_merge($resultTags, static::$method(static::sendAndReceive($connection, $packet, $rtuConverter), $valueTag->address, $tags));
                      }
                    }
                    break;

                  default:
                    if ($v['quantity'] > 50) {
                      foreach ($v['tags'] as $keyTag => $valueTag) {
                        $startAddress = $valueTag->address;
                        $quantity = $valueTag->quantity;
                        $tcpPacket = new static::$modbusFunction[$k]($startAddress, $quantity, $slaveId);
                        $tags = [];
                        $tags[$keyTag] = $valueTag;
                        $resultTags = array_merge($resultTags, static::$method(static::sendAndReceive($connection, $tcpPacket, $rtuConverter), $startAddress, $tags));
                        // dd($method);
                      }
                    } else {
                      $startAddress = $v['startAddress'];
                      $quantity = $v['quantity'];
                      if ($k == 'ReadInputRegistersRequest') {
                        $quantity = $v['quantity'] - 1;
                      }
                      $tcpPacket = new static::$modbusFunction[$k]($startAddress, $quantity, $slaveId);
                      dump($startAddress, $quantity, $slaveId);

                      $resultTags = array_merge($resultTags, static::$method(static::sendAndReceive($connection, $tcpPacket, $rtuConverter), $startAddress, $v['tags']));
                      // if($slaveId == 2){
                      //   dump('ddd');
                      //   dd($resultTags);
                      // }
                    }
                    break;
                }
              }
            }
            if (static::key_check()) {
              if (ScadaActions::where('status', 0)->count() <= 0) {
                static::key_delete();
              }
            }

            $connection->close();
            return $resultTags;
          }
        }
      }
    }
  }


  public static function sendAndReceive($connection, $tcpPacket, $rtuConverter)
  {
    try {
      if ($rtuConverter) {
        $rtuPacket = RtuConverter::toRtu($tcpPacket);
        $binaryData = $connection->sendAndReceive($rtuPacket);
        $response = RtuConverter::fromRtu($binaryData);
      } else {
        $binaryData = $connection->sendAndReceive($tcpPacket);
        $response = ResponseFactory::parseResponseOrThrow($binaryData);
      }
      return  $response;
    } catch (\Exception $exception) {
      static::d('An exception occurred' . PHP_EOL);
      static::d($exception->getMessage() . PHP_EOL);
      static::d($exception->getTraceAsString() . PHP_EOL);
    }
  }
}

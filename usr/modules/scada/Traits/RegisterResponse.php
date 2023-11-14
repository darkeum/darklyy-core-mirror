<?php

/*
* @name        DARKLYY
* @link        https://darklyy.ru/
* @copyright   Copyright (C) 2012-2023 ООО «ПРИС»
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      Komarov Ivan
*/

namespace Scada\Traits;

trait RegisterResponse
{
    public static function getReadInputRegistersRequest($response, $startAddress, $tags, $bytes_tags = [])
    {
        $result = [];
        try {
            if (!empty($response)) {
                foreach ($response as $address => $word) {
                    $currentAddress = $address + $startAddress;
                    if ((isset($tags[$currentAddress]) || isset($bytes_tags[$currentAddress])) && method_exists($word, 'getHighByteAsInt')) {
                        if (!isset($bytes_tags[$currentAddress])) {
                            $tag = $tags[$currentAddress];
                        } else {
                            $tag = $bytes_tags[$currentAddress];
                        }

                        $doubleWord = (isset($response[$address + 1]) &&  method_exists($response, 'getDoubleWordAt')) ? $response->getDoubleWordAt($address) : null;

                        $quadWord = null;
                        if (isset($response[$address + 3])) {
                            $quadWord = $response->getQuadWordAt($address);
                            try {
                                $Int64 = $quadWord->getInt64();
                            } catch (\Exception $e) {
                                $Int64 = '-';
                            }
                        }
                        $highByteAsInt = $word->getHighByteAsInt();
                        $lowByteAsInt = $word->getLowByteAsInt();

                        switch ($tag->endian) {
                            case 'BIG_ENDIAN':
                                $endian = 0 | 1;
                                break;

                            default:
                                $endian = null;
                                break;
                        }

                        $prepare = [
                            'highByte' => '0x' . str_pad(dechex($highByteAsInt), 2, '0') . ' / ' . $highByteAsInt . ' / "&#' . $highByteAsInt . ';"',
                            'lowByte' => '0x' . str_pad(dechex($lowByteAsInt), 2, '0') . ' / ' . $lowByteAsInt . ' / "&#' . $lowByteAsInt . ';"',
                            'highByteBits' => sprintf('%08d', decbin($highByteAsInt)),
                            'lowByteBits' => sprintf('%08d', decbin($lowByteAsInt)),
                            'int16' => $word->getInt16(),
                            'UInt16' => $word->getUInt16(),
                            'int32' => $doubleWord ? $doubleWord->getInt32() : null,
                            'UInt32' => $doubleWord ? $doubleWord->getUInt32() : null,
                            'float' => $doubleWord ? $doubleWord->getFloat($endian) : null,
                            'Int64' => $quadWord ? $Int64 : null,
                        ];


                        $value = $prepare[$tag->type];
                        if ($tag->recalculation == 1) {
                            $value = $tag->coefficient_a * $value + $tag->coefficient_b;
                        }
                        $value = number_format($value, 1);
                        if (!is_null($tag->byte)) {
                            if ($tag->byte <= 7) {
                                $bytes = $prepare['lowByteBits'];
                                $byte_index = 7 - $tag->byte;
                            } else {
                                $bytes = $prepare['highByteBits'];
                                $byte_index = 7 - $tag->byte + 8;
                            }
                            $value = $bytes[$byte_index];
                        } else {
                            $tag_id = $tag->id;
                        }
                        $result[$tag_id] = [
                            'id' => $tag->id,
                            'field_id' => $tag->field_id,
                            'field_type' => $tag->field_type,
                            'type' => $tag->type,
                            'address' => $address,
                            'currentAddress' => $currentAddress,
                            'prepare' => $prepare,
                            'title' => $tag->title,
                            'value' => $value,
                        ];

                    }
                }
            }
        } catch (\Exception $exception) {
            static::d2('An exception occurred' . PHP_EOL);
            static::d2($exception->getMessage() . PHP_EOL);
            static::d2($exception->getTraceAsString() . PHP_EOL);
        }
        return $result;
    }
    static $debug2 = false;

    protected static function d2($message = null)
    {
        if (static::$debug2) {
            dump($message);
        }
    }

    public static function getReadWriteMultipleRegistersRequest($response, $startAddress, $tags, $bytes_tags = [])
    {
        return static::getReadInputRegistersRequest($response, $startAddress, $tags, $bytes_tags);
    }

    public static function getReadHoldingRegistersRequest($response, $startAddress, $tags, $bytes_tags = [])
    {
        return static::getReadInputRegistersRequest($response, $startAddress, $tags, $bytes_tags);
    }

    public static function getReadWriteSingleCoilRequest($response, $startAddress, $tags, $bytes_tags = [])
    {
        return static::getReadInputDiscretesRequest($response, $startAddress, $tags, $bytes_tags);
    }

    public static function getReadInputDiscretesRequest($response, $startAddress, $tags, $bytes_tags = [])
    {
        $result = [];
        try {
            if (!empty($response) && method_exists($response, 'getCoils')) {
                $coil = $response->getCoils();
                foreach ($response as $address => $word) {
                    $currentAddress = $address + $startAddress;
                    if (isset($tags[$currentAddress])) {
                        $tag = $tags[$currentAddress];
                        $result[$tag->id] = [
                            'id' => $tag->id,
                            'field_id' => $tag->field_id,
                            'field_type' => $tag->field_type,
                            'type' => $tag->type,
                            'address' => $address,
                            'currentAddress' => $currentAddress,
                            'title' => $tag->title,
                            'value' => $tag->invert ? !$coil[$address] : $coil[$address],
                        ];
                    }
                }
            }
        } catch (\Exception $exception) {
            static::d('An exception occurred' . PHP_EOL);
            static::d($exception->getMessage() . PHP_EOL);
            static::d($exception->getTraceAsString() . PHP_EOL);
        }
        return $result;
    }
}

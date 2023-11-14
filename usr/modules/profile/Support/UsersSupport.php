<?php

namespace Profile\Support;

class UsersSupport
{
    static function check_data($request, $id = null)
    {
        $userFind = new \Boot\Models\Users;
        $userWhereFlag = false;

        if (!empty($request["email"])) {
            if (!preg_match('/[^@ \t\r\n]+@[^@ \t\r\n]+\.[^@ \t\r\n]+/', $request["email"]))
                throw new \Exception("Значение `{$request["email"]}` не является почтой!");

            $userWhereFlag = true;
            $userFind->orWhere("email", $request["email"]);
        }
        if (!empty($request["phone"])) {
            if (!preg_match('/^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/', $request["phone"]))
                throw new \Exception("Значение `{$request["phone"]}` не является телефоном!");

            $userWhereFlag = true;
            $userFind->orWhere("phone", $request["phone"]);
        }

        if ($userWhereFlag) {
            if (!is_null($id)) {
                $userFind->where("id", "!=", $id);
            }
            if ($userFind->exists()) throw new \Exception("Такой пользователь с такими данными уже есть!");
        }
    }

    static function avatar_base64($uid, $file)
    {
        $avatar = ROOT . "files/avatars/users/av{$uid}.jpg";
        if (empty($file)) {
            unlink($avatar);
        } else {
            $ifp = fopen($avatar, 'wb');
            $data = explode(',', $file);
            fwrite($ifp, base64_decode($data[1]));
            fclose($ifp);
        }
    }
}
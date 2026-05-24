<?php

class encrypter {
 
    private static $Key = "gP67M24e$+";
 
    /* public static function encrypt ($input) {
        $output = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5(Encrypter::$Key), $input, MCRYPT_MODE_CBC, md5(md5(Encrypter::$Key))));
        return $output;
    }
 
    public static function decrypt ($input) {
        $output = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5(Encrypter::$Key), base64_decode($input), MCRYPT_MODE_CBC, md5(md5(Encrypter::$Key))), "\0");
        return $output;
    } */

    public static function encrypt($input)
    {
        $ciphering = "AES-128-CTR"; // it stores the cipher method
        $option = 0; // it holds the bitwise disjunction of the flags
        $encryption_iv = '1234567890123456'; // it hold the initialization vector which is not null
        // $encryption_key = "gP67M24e$+";
        $output = openssl_encrypt($input, $ciphering, self::$Key, $option, $encryption_iv);
        return $output;
    }

    public static function decrypt($input)
    {
        $ciphering = "AES-128-CTR"; // it stores the cipher method
        $option = 0; // it holds the bitwise disjunction of the flags
        $decryption_iv = '1234567890123456'; // it hold the initialization vector which is not null
        // $decryption_key = "gP67M24e$+";
        $output = openssl_decrypt($input, $ciphering, self::$Key, $option, $decryption_iv);
        return $output;
    }
 
}
?>
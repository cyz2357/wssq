<?php
// +----------------------------------------------------------------------
// | TOPThink [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;
class Rsa {      
    private static $PRIVATE_KEY = '-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQDBiYEk6LHMqqUm6WJCcSNfjlPZXPj/zHjmuVuU/QLE/yKqv2YE
iPiGxaajZdBL4WUNRQxO4Dt4MDrjN43CsAzQj6OT/fDgroPERccBnwAZQr5FTR4G
FfhxcoWxT/2nfmIVI7nHoJSeV7nHHwBBwagb4Z5EDrQDKr3vsumk9DY98wIDAQAB
AoGAc1ACaoNarJ9gF3cm3H+cm+qpevEySvcBcjcuoe6+H7IEbcfsePdDXMmXJ/QZ
z5xUITvqwXRI+quB6q/G/yD2AKLfn870vbhoqLqw6m7td/slldRtMEBlXGKkH+Cq
ox4nqhY6jfTN0+3CsJb4ArDKCPm855wTaSizMcaxv+gya4ECQQD7WHFNBzfvZ+E3
aojgIj99YEEKqcx5VVgWdxp6EP15ewCgrfRcMXkY/swuuNv/klbQNeiPhkxWN64U
IHWbhWcrAkEAxR8CSY9QBdHf/fyQCJwJBdeV18Ei0A1T8b2yjKNDoos92t3aZ4C4
tL+AaJdexvv0JE2MKZAtfmA3ENZ2qdAgWQJAHaRbkFsGlLxqii+NPumrFS0x3oER
9UlEXznirHyueBIyi8CYLftDrdf22pfAkk5+4IVLDQoonI5qgEMnu5SrCwJAHFhp
K8q/5xZW717yF0rYwU5O7ceICgLgn1J36mFPyH/dVSuxzFBmPxeKj3+00UhJwy6j
iRaCdDnBsVolliWngQJAK4RkiGl60YP9jNdMB+2F63ainR8LGtCr8OK03YNVB3jT
V1Zs6L3WxhC3MBsoquhf2LsjdvCXZGP+0RuDxcFwsQ==
-----END RSA PRIVATE KEY-----';
    private static $PUBLIC_KEY = '-----BEGIN PUBLIC KEY-----MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDBiYEk6LHMqqUm6WJCcSNfjlPZXPj/zHjmuVuU/QLE/yKqv2YEiPiGxaajZdBL4WUNRQxO4Dt4MDrjN43CsAzQj6OT/fDgroPERccBnwAZQr5FTR4GFfhxcoWxT/2nfmIVI7nHoJSeV7nHHwBBwagb4Z5EDrQDKr3vsumk9DY98wIDAQAB-----END PUBLIC KEY-----';     
  
    /**
     * 获取私钥      
     * @return bool|resource      
     */      
    private static function getPrivateKey()   
    {          
        $privKey = self::$PRIVATE_KEY;          
        return openssl_pkey_get_private($privKey);      
    }      
  
    /**      
     * 获取公钥      
     * @return bool|resource      
     */      
    private static function getPublicKey()  
    {          
        $publicKey = self::$PUBLIC_KEY;          
        return openssl_pkey_get_public($publicKey);      
    }      
  
    /**      
     * 私钥加密      
     * @param string $data      
     * @return null|string      
     */      
    public static function privEncrypt($data = '')      
    {          
        if (!is_string($data)) {              
            return null;         
        }          
        return openssl_private_encrypt($data,$encrypted,self::getPrivateKey()) ? base64_encode($encrypted) : null;      
    }      
  
    /**      
     * 公钥加密      
     * @param string $data      
     * @return null|string      
     */      
    public static function publicEncrypt($data = '')     
    {          
        if (!is_string($data)) {              
            return null;          
        }          
        return openssl_public_encrypt($data,$encrypted,self::getPublicKey()) ? base64_encode($encrypted) : null;      
    }      
  
    /**      
     * 私钥解密      
     * @param string $encrypted      
     * @return null      
     */      
    public static function privDecrypt($encrypted = '')      
    {          
        if (!is_string($encrypted)) {              
            return null;          
        }          
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, self::getPrivateKey())) ? $decrypted : null;      
    }      
  
    /**      
     * 公钥解密      
     * @param string $encrypted      
     * @return null      
     */      
    public static function publicDecrypt($encrypted = '')      
    {          
        if (!is_string($encrypted)) {              
            return null;          
        }          
    return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, self::getPublicKey())) ? $decrypted : null;      
    }  
} 

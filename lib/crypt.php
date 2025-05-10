<?php

$cryptKey = $config["cookieKey"];

/**
 * AES text encryption with SHA256 HMAC signing
 */
function aes_encrypt(string $plaintext, $useHmac=false) {
  global $cryptKey;

  $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
  $iv = openssl_random_pseudo_bytes($ivlen);
  $ciphertext_raw = openssl_encrypt($plaintext, $cipher, $cryptKey, $options=OPENSSL_RAW_DATA, $iv);
  
  if ($useHmac) {
    $hmac = hash_hmac('sha256', $ciphertext_raw, $cryptKey, $as_binary=true);
    return base64_encode( $iv.$hmac.$ciphertext_raw );
  } else {
    return base64_encode( $iv.$ciphertext_raw );
  }
}

/**
 * AES text decryption with SHA256 HMAC timing-attack-safe verification
 */
function aes_decrypt(string $ciphertext, $useHmac=false) {
  global $cryptKey;

  $c = base64_decode($ciphertext);
  $ivlen = openssl_cipher_iv_length($cipher="AES-128-CBC");
  $iv = substr($c, 0, $ivlen);

  if ($useHmac) {
    $hmac = substr($c, $ivlen, $sha2len=32);
    $ciphertext_raw = substr($c, $ivlen+$sha2len);
  } else {
    $ciphertext_raw = substr($c, $ivlen);
  }
  
  $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $cryptKey, $options=OPENSSL_RAW_DATA, $iv);

  if (!$useHmac) {
    return $original_plaintext;
  }

  $calcmac = hash_hmac('sha256', $ciphertext_raw, $cryptKey, $as_binary=true);

  if (hash_equals($hmac, $calcmac)) {
    return $original_plaintext;
  } else {
    throw new Exception("InvalidKeyError");
  }
}

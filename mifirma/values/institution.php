<?php
require_once("../../../../wp-load.php");

$data = get_values();

define("MIFIRMACR_LISTEN_URL",$data[0]->mifirmacr_listen_url);
define("MIFIRMACR_ALGORITHM",$data[0]->mifirmacr_algorithm);
define("MIFIRMACR_INSTITUTION",$data[0]->mifirmacr_institution);
define("MIFIRMACR_PRIVATE_KEY", $data[0]->mifirmacr_private_key);
define("MIFIRMACR_PUBLIC_CERTIFICATE", $data[0]->mifirmacr_public_certificate);
define("MIFIRMACR_SERVER_PUBLIC_KEY", $data[0]->mifirmacr_server_public_key);


// define("MIFIRMACR_LISTEN_URL","http://05e2a59c.ngrok.io/wp_mi_firma/");
// define("MIFIRMACR_ALGORITHM","sha256");
// define("MIFIRMACR_INSTITUTION","e5235ab9-df68-4529-8c13-573c07e4536f");
//
// define("MIFIRMACR_PRIVATE_KEY", "-----BEGIN PRIVATE KEY-----
// MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDW68fks7W3McTn
// +VkHTDalo6uS2veAptotjsGnSAY5Cm+r3OTEDSDTd3FiD/IPQW0vOyINduHRi5cZ
// wmk3ORvjeCUhi+HIlyj+W1aE2dZ40AABlcgi3lidn51oNoFQ6VgM3l5DNcF8e/zS
// YfykEfiftxss4GD0fsUOdBY5TkCW+QNCFTA9Nlzt5yTOo8NQzQ7xHeTCF3zfJEmL
// G02bYEMch5fKtCYXWJl4lkEuBtWt3UCgf5S2/yt3LKhTDoJqmMjwiiNe56eK4hoc
// GwWsFBAFKUHlSYtsTQVAG/xfsRYXCiShdSWkpgucI4UFe0kqcFJ9he5Y8Mn0Ff48
// jsy44+K/AgMBAAECggEBAKGBo8Cgr8CP/PmZ7qZGRpao7Qk51V5vhGtR6QLtZAe/
// aPXM1HjjqLRhAOqBMxT1xLgGhut5V+9hJ9ZquyiNmQCSS8zu0mgr99lVCCpjjQFS
// xX5rcW+KiStCREaJFxqXj9xzyoBVSCZlAjOTAQjGnIBhWGGb6CAzl5/p6r17qZwI
// +jc1jltdxv3tKMrkSurmgWbewovhv0A7xTD3N/akwbNq2uoe5VupkB/ZPN8vCNv9
// U4DsgrSBJ0ranTPLySS00grZlZDfjDXgrU5Ndx8OgVQc6Ra6u3WaQIQzlfmFQ7gz
// DqLq7i5PNGh8J1N26FqzAVGoaDc4sXM8RFZ95GjRrRkCgYEA6/+ZZra+nCNJAT/W
// osOAklpZAz1IWOKQC5ZrxiD1reXoDZkf6VDdrC670uguW1dFnpmliwgf8qNHUNZ+
// NVMbG6W0i1Da0Vr3XZzqWFFkYO9WsF639+5fimMT26isBtxZj6U38bKVBGOtf5MQ
// skfVu4HhWfUYdGc2tOpyqqN2LZ0CgYEA6SLeusivdCxjNbQ7b75dzMXCQkm7Qy+C
// e7HYwBSrsTGWrOVpov4YRL/JCMSQlUT0S+NenIipYaHwCgHTqHn/9SRO4432upP3
// Ag1pIZntkKRg5VoIUJXzD+J5PgAVKXDUMeECNEmS+J2WLsi1WVuLrSjja3pXwZ0K
// KY/PUlrCkQsCgYEAwjkpVo8vb/DaNF0FmA6t0cTpXPEiiYRsaBzztauKhgOgZxCO
// YsZaqUoM6haLkEDS2yQ4SaP2JwqZtr3QQLKUrLxDSiTQ8VucGT763pSdt+lBvwU1
// Aqb7mjjQLwcyDLNsQfwHfrMqUOJ393ZhV1gnXpoVjKNZ7PLE4z/P/v9oU0ECgYAg
// OrXX1I6M3OYKMpRU6lhFSsGEMHU24IaFWxy/0Ru7L0PJOx3TbpUkS+8ayzHBsPqk
// 0xXtRedEnAJ3H7GHBPahiRdu1d1aBcKMAhaakpEJAfBzRHMJ0PD9LS3dqF1EkViE
// XLrVR6aNwBtW9GA9ri4tDg4CebNQDMmu3TaoB+wF6QKBgDIkRQIFPdmU8Fi8lTHE
// YQ4agLsXrcqyqTAo6uIEQuE+ZdnUKbf0jPBgn9RWUDVPpzEwUfpmKgMxNn1O4lUG
// 3xDZtPf3jMijN8lcgw4sj+tsXm78tMplopKTp6b2OEYLeRSJEPnUAV+8paj71acB
// tNQ8lLZU4mm96VpPf4w/1AzW
// -----END PRIVATE KEY-----" );
//
//
// define("MIFIRMACR_PUBLIC_CERTIFICATE", '-----BEGIN CERTIFICATE-----
// MIIEPDCCAiQCFQoj3KyDg0ry06+7v52GXry6RkcrjjANBgkqhkiG9w0BAQsFADBU
// MQswCQYDVQQGEwJDUjEgMB4GA1UECgwXRmlybWEgRGlnaXRhbCBtaWZpcm1hY3Ix
// IzAhBgNVBAMMGkNBIEZpcm1hIERpZ2l0YWwgbWlmaXJtYWNyMB4XDTE3MDUxNzIw
// MjIyMFoXDTE4MDUxNzIwMjIyMFowYDELMAkGA1UEBhMCQ1IxETAPBgNVBAgMCFNh
// biBKb3NlMRMwEQYDVQQHDApDb3N0YSBSaWNhMQ0wCwYDVQQKDAR2aW5pMQswCQYD
// VQQLDAJORDENMAsGA1UEAwwEdmluaTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCC
// AQoCggEBANbrx+SztbcxxOf5WQdMNqWjq5La94Cm2i2OwadIBjkKb6vc5MQNINN3
// cWIP8g9BbS87Ig124dGLlxnCaTc5G+N4JSGL4ciXKP5bVoTZ1njQAAGVyCLeWJ2f
// nWg2gVDpWAzeXkM1wXx7/NJh/KQR+J+3GyzgYPR+xQ50FjlOQJb5A0IVMD02XO3n
// JM6jw1DNDvEd5MIXfN8kSYsbTZtgQxyHl8q0JhdYmXiWQS4G1a3dQKB/lLb/K3cs
// qFMOgmqYyPCKI17np4riGhwbBawUEAUpQeVJi2xNBUAb/F+xFhcKJKF1JaSmC5wj
// hQV7SSpwUn2F7ljwyfQV/jyOzLjj4r8CAwEAATANBgkqhkiG9w0BAQsFAAOCAgEA
// Hy7/zGzY+S09uEx4tKQ+nYIJDVs4FOmLXIO+jTEPPPgMIBWWmLrGHCbeFk3IxdGr
// /Mehf7VKj3ju/OpYRLUOf9EMygJAn//TqRKgoZro73pEGgeHwrUmmOGYWSji03lZ
// bKBsxGZIj1ws9iHLAwD9gnujdvuWq0ulax4SdtuTZLfWHKS8YWMRS9e6l2LUGgey
// WzHKyRAoojHAbcY/Q4+mz2eQmQZuGLfkK388DvF1FBck4MCpWJXi8/qTTodFUesV
// NIfllKLaX59cpHw4nIs6X5yl2tkI9S3p3tGtsiPay+fQTo+D2+uW9L2VixerF2wl
// XWjeqDcm2Jebei2Df2qcgUKQaQOm1Ppm4qRoKTbVQRFC6HAQ8TH240IUHBTCgfL1
// DpHfZ4Mb/+8ixNm8UoFMRf+4lRoS/UuFhqtO7oOPG6jP5kv0uAV3FB674pnpZWWc
// ePSJbLvuB6GKw/9WFsyaUCXyUW3ADVAcs/TnnrhtxYgYb4M/NOIaXYz10i2/z0sh
// 2sy9SBsQiKX+vmoZgAyRwoDEIigV0frohMAJ80Vc9G5EulYrfIEJBm4ejsxoOnv7
// lfi4NaQpZrODzfbj4fM96tNShV7KP72/xRUO3CsuOqa0X9vAAsRfR229QmQC9Yx5
// sRoeAaQUKiXjErKDy7vh0qPgk3+AP/D+suaKlK7BAkQ=
// -----END CERTIFICATE-----');
//
//
// define("MIFIRMACR_SERVER_PUBLIC_KEY", "-----BEGIN PUBLIC KEY-----
// MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAthWNSExR4y4lyFSedxC9
// 2El2m494+Sqj87J8uvpQygptTsUOJU9tswGjlkXuU8+7wLTYOWgHvdG8ZVfXO0Tq
// t5mrliolv1tjHZF93GiTbckDVcB4d8TuM7mk4Tz0TFtD0ke9UfidhDg6conNRfII
// brFsPvSv3mu9AZGicOKYA8O/JibO0q9ALb99CsW6+GCIUzofltopxlUhAOTHHBnO
// +Eabw21ZBX8PpkYe8VYGYk/kCajZUrRmHKsA20jskz6N30scKh9hAzMm+/a1e26d
// 0p2bhNW8VrreFEFoaZu6/lKPuzXqNkmUU7WGo5USOm67gGtnKvPotIJb/sIR9dfx
// xQIDAQAB
// -----END PUBLIC KEY-----");

function get_values()
{
  global $wpdb;
  $table_name = $wpdb->prefix . 'mifirma_institutions';
  $data = $wpdb->get_results( 'SELECT * FROM '.$table_name.'', OBJECT_K );
  $values = [];
  foreach ($data as $key => $value) {
    array_push($values, $value);
  }
  return $values;
}
 ?>

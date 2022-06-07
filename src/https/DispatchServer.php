<?php

namespace TeyvatPS\https;

use labalityowo\Bytebuffer\Buffer;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\HttpServer;
use React\Http\Message\Response;
use React\Socket\SocketServer;
use TeyvatPS\Config;
use TeyvatPS\utils\Crypto;
use TeyvatPS\utils\Logger;

class DispatchServer
{
    /**
     * @var \Closure[]
     */
    private static array $routes = [];

    public static function init(): void
    {
        self::registerRoutes();
        $httpServer = new HttpServer(function (ServerRequestInterface $request) {
            $response = new Response(200, ["Content-Type" => "text/html"], json_encode(['code' => 0, 'message' => 'OK']));
            try {
                $url = $request->getUri()->getPath();
                if ($url === '/' || $url === '/favicon.ico') {
                    $response = new Response(200, ["Content-Type" => "image/x-icon"]);
                } else {
                    $route = self::$routes[$url] ?? null;
                    $response = $route !== null ? ($route)($request) : $response;
                }
            } catch (\Throwable $throwable) {
                Logger::log($throwable->getMessage());
            }
            return $response;
        });

        $httpServer->listen(new SocketServer('127.0.0.1:80'));
        $httpServer->listen(new SocketServer('tls://0.0.0.0:443', [
            'tls' => [
                'local_cert' => Config::DATA_FOLDER . 'cert/cert.pem',
            ],
        ]));
    }

    public static function registerRoutes(): void
    {
        //dispatch
        self::$routes['/query_region_list'] = function (ServerRequestInterface $request) {
            $rsp = new \QueryRegionListHttpRsp();
            $rsp->setRegionList(
                [
                    (new \RegionSimpleInfo())
                        ->setType('DEV_PUBLIC')
                        ->setName('os_teyvatps')
                        ->setTitle('TeyvatPS')
                        ->setDispatchUrl('https://127.0.0.1/query_cur_region')
                ]
            );

            $customConfig = Buffer::new('{\"sdkenv\":\"2\",\"checkdevice\":\"false\",\"loadPatch\":\"false\",\"showexception\":\"false\",\"regionConfig\":\"pm|fk|add\",\"downloadMode\":\"0\"}');
            Crypto::xorBuffer($customConfig, Crypto::$ec2bKey);

            $rsp->setClientSecretKey(Crypto::$ec2bBin->toString());
            $rsp->setClientCustomConfigEncrypted($customConfig->toString());

            return new Response(200, ["Content-Type" => "application/json"], base64_encode($rsp->serializeToString()));
        };

        self::$routes['/query_cur_region'] = function (ServerRequestInterface $request) {
            $config = $request->getQueryParams();

            $rsp = new \QueryCurrRegionHttpRsp();
            $regionInfo = new \RegionInfo();
            $regionInfo->setGateserverIp(Config::HOST);
            $regionInfo->setGateserverPort(Config::PORT);
            $regionInfo->setSecretKey(Crypto::$ec2bBin->toString());

            $customConfig = Buffer::new('{"coverSwitch":[8],"perf_report_config":"http:\/\/127.0.0.1:80\/config\/verify","perf_report_record_url":"http:\/\/127.0.0.1:80\/dataUpload"}');
            Crypto::xorBuffer($customConfig, Crypto::$ec2bKey);

            $rsp->setRegionInfo($regionInfo);
            $rsp->setClientRegionCustomConfigEncrypted($customConfig->toString());
            if (isset($config['version']) && (str_contains($config['version'], "2.7.5") || str_contains($config['version'], "2.8"))) {
                $regionInfo->setSecretKey(0);
                openssl_public_encrypt($rsp->serializeToString(), $encrypted, Crypto::$publicKey, OPENSSL_PKCS1_PADDING);

                return new Response(200, ["Content-Type" => "application/json"], json_encode([
                    "content" => base64_encode($encrypted),
                    "sign" => base64_encode("hello")
                ]));
            } else {
                return new Response(200, ["Content-Type" => "application/json"], base64_encode($rsp->serializeToString()));
            }
        };

        //accounts
        self::$routes["/account/risky/api/check"] = function (ServerRequestInterface $request) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode([
                "retcode" => 0,
                "message" => "OK",
                "data" => [
                    "id" => "none",
                    "action" => "ACTION_NONE",
                    "geetest" => null
                ]
            ]));
        };

        self::$routes['/hk4e_global/combo/granter/api/compareProtocolVersion'] = function (
            ServerRequestInterface $request
        ) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode(
                [
                    "retcode" => 0,
                    "message" => "OK",
                    "data" => [
                        "modified" => true,
                        "protocol" => [
                            "id" => 0,
                            "app_id" => 4,
                            "language" => "vi",
                            "user_proto" => "",
                            "priv_proto" => "",
                            "major" => 4,
                            "minimum" => 0,
                            "create_time" => "0",
                            "teenager_proto" => "",
                            "third_proto" => ""
                        ]
                    ]
                ]
            ));
        };

        self::$routes['/hk4e_global/combo/granter/login/beforeVerify'] = function (ServerRequestInterface $request) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode([
                "retcode" => 0,
                "message" => "OK",
                "data" => [
                    "is_heartbeat_required" => false,
                    "is_realname_required" => false,
                    "is_guardian_required" => false
                ]
            ]));
        };

        self::$routes['/hk4e_global/combo/granter/login/v2/login'] = function (ServerRequestInterface $request) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode([
                "retcode" => 0,
                "message" => "OK",
                "data" => [
                    "combo_id" => "89858023",
                    "open_id" => "129399082",
                    "combo_token" => "580729acc024f02927c94ab18a88bf171c40e0fc",
                    "data" => [
                        "guest" => false
                    ],
                    "heartbeat" => false,
                    "account_type" => 1
                ]
            ]));
        };

        self::$routes['/hk4e_global/mdk/shield/api/login'] = function (ServerRequestInterface $request) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode([
                "retcode" => 0,
                "message" => "OK",
                "data" => self::getSimpleAccountInfo(69, 'labalityowo'),
                "device_grant_required" => false,
                "safe_moblie_required" => false,
                "realperson_required" => false,
                "reactivate_required" => false,
                "realname_operation" => "None"
            ]));
        };

        self::$routes['/hk4e_global/mdk/shield/api/verify'] = function (ServerRequestInterface $request) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode([
                "retcode" => 0,
                "message" => "OK",
                "data" => self::getSimpleAccountInfo(69, 'labalityowo'), //kek no db
                "device_grant_required" => false,
                "safe_moblie_required" => false,
                "realperson_required" => false,
                "reactivate_required" => false,
                "realname_operation" => "None"
            ]));
        };

        self::$routes['/combo/box/api/config/sdk/combo'] = function (ServerRequestInterface $request) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode([
                "vals" => [
                    "disable_email_bind_skip" => "false",
                    "email_bind_remind" => "true",
                    "email_bind_remind_interval" => "7"
                ]
            ]));
        };


        self::$routes['/admin/mi18n/plat_oversea/m2020030410/m2020030410-version.json'] = function (
            ServerRequestInterface $request
        ) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode([
                "version" => "54"
            ]));
        };

        self::$routes['/hk4e_global/combo/granter/api/getConfig'] = function (ServerRequestInterface $request) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode([
                "announce_url" => "https://localhost/hk4e/announcement/index.html",
                "disable_ysdk_guard" => false,
                "enable_announce_pic_popup" => true,
                "log_level" => "INFO",
                "protocol" => true,
                "push_alias_type" => 2,
                "qr_enabled" => false
            ]));
        };

        self::$routes['/hk4e_global/mdk/shield/api/loadConfig'] = function (ServerRequestInterface $request) {
            return new Response(200, ["Content-Type" => "application/json"], json_encode([
                "client" => "PC",
                "disable_mmt" => false,
                "disable_regist" => false,
                "enable_email_captcha" => false,
                "enable_ps_bind_account" => false,
                "game_key" => "hk4e_global",
                "guest" => false,
                "id" => 6,
                "identity" => "I_IDENTITY",
                "ignore_versions" => "",
                "name" => "原神海外",
                "scene" => "S_NORMAL",
                "server_guest" => false,
                "thirdparty" => [
                    "fb",
                    "tw"
                ],
                "thirdparty_ignore" => [
                    "fb" => "",
                    "tw" => ""
                ]
            ]));
        };
    }


    public static function getSimpleAccountInfo(int $uid, string $name): array
    {
        return [
            "account" => [
                "uid" => (string)$uid,
                "name" => $name,
                "email" => $name,
                "mobile" => "",
                "is_email_verify" => "0",
                "realname" => "",
                "identity_card" => "",
                "token" => "token",
                "safe_mobile" => "",
                "facebook_name" => "",
                "google_name" => "",
                "twitter_name" => "",
                "game_center_name" => "",
                "apple_name" => "",
                "sony_name" => "",
                "tap_name" => "",
                "country" => "VN",
                "reactivate_ticket" => "",
                "area_code" => "**"
            ],
        ];
    }
}
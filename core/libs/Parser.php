<?php

class Parser {

	private static string $url = "";

    private static string $user_agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/99.0.4844.74 Safari/537.36";

    private static int $timeout = 5;
    private static int $connecttimeout = 5;
    private static bool $head = false;

    private static array $cookie = array("file" => false, "session" => false);
    private static array $proxy = array("ip" => false, "port" => false, "type" => false);

    private static array|bool $headers = false;
    private static array|bool $post = false;

    private static array $error_codes = [
        "CURLE_UNSUPPORTED_PROTOCOL",
        "CURLE_FAILED_INIT",
         // Тут более 60 элементов, в архиве вы найдете весь список
        "CURLE_FTP_BAD_FILE_LIST",
        "CURLE_CHUNK_FAILED"
    ];

    /**
	 * $useragent – нам важно иметь возможность устанавливать заголовок User-Agent, так мы сможем сделать наши обращения к серверу похожими на обращения из браузера;
	 * $timeout – будет отвечать за время выполнения запроса на сервер;
	 * $connecttimeout – так же важно указывать время ожидания соединения;
	 * $head – если нам потребуется проверить только заголовки, которые отдаёт сервер на наш запрос этот параметр нам просто будет необходим;
	 * $cookie_file – тут всё просто: файл, в который будут записывать куки нашего донора контента и при обращении передаваться;
	 * $cookie_session – иногда может быть необходимо, запрещать передачу сессионных кук;
	 * $proxy_ip – параметр говорящий, IP прокси-сервера, мы сегодня спарсим пару страниц, но если необходимо несколько тысяч, то без проксей никак;
	 * $proxy_port – соответственно порт прокси-сервера;
	 * $proxy_type – тип прокси CURLPROXY_HTTP, CURLPROXY_SOCKS4, CURLPROXY_SOCKS5, CURLPROXY_SOCKS4A или CURLPROXY_SOCKS5_HOSTNAME;
	 * $headers – выше мы указали параметр, отвечающий за заголовок User-Agent, но иногда нужно передать помимо его и другие, для этого нам потребуется массив заголовков;
	 * $post – для отправки POST запроса.
	 */

    public static function getPage($params = []) {
        if ($params) {
            if (!empty($params["url"])) {
                self::$url = $params["url"];

                $useragent      = !empty($params["useragent"]) ? $params["useragent"] : self::$user_agent;

                $timeout        = !empty($params["timeout"]) ? $params["timeout"] : self::$timeout;
                $connecttimeout = !empty($params["connecttimeout"]) ? $params["connecttimeout"] : self::$connecttimeout;
                $head           = !empty($params["head"]) ? $params["head"] : self::$head;

                $cookie_file    = !empty($params["cookie"]["file"]) ? $params["cookie"]["file"] : self::$cookie["file"];
                $cookie_session = !empty($params["cookie"]["session"]) ? $params["cookie"]["session"] : self::$cookie["session"];

                $proxy_ip       = !empty($params["proxy"]["ip"]) ? $params["proxy"]["ip"] : self::$proxy["ip"];
                $proxy_port     = !empty($params["proxy"]["port"]) ? $params["proxy"]["port"] : self::$proxy["port"];
                $proxy_type     = !empty($params["proxy"]["type"]) ? $params["proxy"]["type"] : self::$proxy["type"];

                $headers        = !empty($params["headers"]) ? $params["headers"] : self::$headers;

                $post           = !empty($params["post"]) ? $params["post"] : self::$post;

                // будет очищать файл с куками при запросе
                if ($cookie_file) {
                    file_put_contents(__DIR__."/".$cookie_file, "");
                }

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, self::$url);
                curl_setopt($ch, CURLINFO_HEADER_OUT, true);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

                // $useragent, $timeout и $connecttimeout
                curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connecttimeout);

                // чтобы получить заголовки ответа
                if ($head) {
                    curl_setopt($ch, CURLOPT_HEADER, true);
                    curl_setopt($ch, CURLOPT_NOBODY, true);
                }

                // Для работы со ссылками с SSL сертификатом
                if (strpos(self::$url, "https") !== false) {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
                }

                // частая проблема, когда куки не сохраняются. Одной из основных причин может быть указание относительного пути
                if ($cookie_file) {
                    curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__."/".$cookie_file);
                    curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__."/".$cookie_file);

					if ($cookie_session) {
                        curl_setopt($ch, CURLOPT_COOKIESESSION, true);
                    }
                }

                // прокси, заголовки и возможность отправки запросов POST
                if ($proxy_ip && $proxy_port && $proxy_type) {
                    curl_setopt($ch, CURLOPT_PROXY, $proxy_ip.":".$proxy_port);
                    curl_setopt($ch, CURLOPT_PROXYTYPE, $proxy_type);
                }

                if ($headers) {
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                }

                if ($post) {
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                }

                $content = curl_exec($ch);
                $info    = curl_getinfo($ch);

                $error = [];

                if ($content === false) {
                    $data = false;

                    $error["message"] = curl_error($ch);
                    $error["code"]      = self::$error_codes[
                        curl_errno($ch)
                    ];
                }
                else {
                    $data["content"] = $content;
                    $data["info"]    = $info;
                }

                curl_close($ch);

                return [
                    "data"  => $data,
                    "error" => $error
                ];
			}
		}

		return false;
	}

}

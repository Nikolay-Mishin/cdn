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

/*********************************************
* #### Imap_parser.php v0.0.1 ####
* IMAP mailbox parser using PHP.
* Return Array or JSON.
* Coded by @bachors 2016.
* https://github.com/bachors/Imap_parser.php
* Updates will be posted to this site.
- data:
	- email:
		- hostname
		- username
		- password
	- pagination:
		- sort
		- limit
		- offset
	
- result:
	- status
	- email
	- count
	- inbox:
		- id
		- subject
		- from
		- email
		- date
		- message
		- image
	- pagination
		- sort
		- limit
		- offset
			- back
			- next
		
*********************************************/

class Imap_parser {
    
	function inbox($data)
	{

		$result = array();
		
		$imap = imap_open($data['email']['hostname'], $data['email']['username'], $data['email']['password']) or die ('Cannot connect to yourdomain.com: ' . imap_last_error());
		
		if ($imap) {
			
			$result['status'] = 'success';
			$result['email']  = $data['email']['username'];
			
			$read = imap_search($imap, 'ALL');
			
			if($data['pagination']['sort'] == 'DESC'){
				rsort($read);
			}
			
			$num = count($read);
			
			$result['count'] = $num;
			
			$stop = $data['pagination']['limit'] + $data['pagination']['offset'];
			
			if($stop > $num){
				$stop = $num;
			}
			
			for ($i = $data['pagination']['offset']; $i < $stop; $i++) {
				
				$overview   = imap_fetch_overview($imap, $read[$i], 0);
				$message    = imap_body($imap, $read[$i], 0);
				$header     = imap_headerinfo($imap, $read[$i], 0);
				$mail       = $header->from[0]->mailbox . '@' . $header->from[0]->host;
				$image = '';
				
				$message = preg_replace('/--(.*)/i', '', $message);
				$message = preg_replace('/X\-(.*)/i', '', $message);
				$message = preg_replace('/Content\-ID\:/i', '', $message);
				
				$msg = '';            
				
				if (preg_match('/Content-Type/', $message)) {
					$message = strip_tags($message);
					$content = explode('Content-Type: ', $message);
					foreach ($content as $c) {
						if (preg_match('/base64/', $c)) {
							$b64 = explode('base64', $c);
							if (preg_match('/==/', $b64[1])) {
								$str = explode('==', $b64[1]);
								$dec = $str[0];
							} else {
								$dec = $b64[1];
							}
							if (preg_match('/image\/(.*)\;/', $c, $mime)) {
								$image = 'data:image/' . $mime[1] . ';base64,' . trim($dec);
							}
						} else {
							if (!empty($c)) {
								$msg = $c;
							}
						}
					}
				} else {
					$msg = $message;
				}
				
				$msg = preg_replace('/text\/(.*)UTF\-8/', '', $msg);
				$msg = preg_replace('/text\/(.*)\;/', '', $msg);
				$msg = preg_replace('/charset\=(.*)\"/', '', $msg);
				$msg = preg_replace('/Content\-Transfer\-Encoding\:(.*)/i', '', $msg);
				
				$result['inbox'][] = array(
					'id' => $read[$i],
					'subject' => strip_tags($overview[0]->subject),
					'from' => $overview[0]->from,
					'email' => $mail,
					'date' => $overview[0]->date,
					'message' => trim($msg),
					'image' => $image
				);
				
				$result['pagination'] = array(
					'sort' => $data['pagination']['sort'],
					'limit' => $data['pagination']['limit'],
					'offset' => array(
						'back' => ($data['pagination']['offset'] == 0 ? null : $data['pagination']['offset'] - $data['pagination']['limit']),
						'next' => ($data['pagination']['offset'] < $num ? $data['pagination']['offset'] + $data['pagination']['limit'] : null)
					)
				);
				
			}
			
			imap_close($imap);
			
		} else {
			$result['status'] = 'error';
		}
		
		return $result;
		
	}

}

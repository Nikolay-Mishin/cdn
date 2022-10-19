<?php
require_once "core/libs/Parser.php";

class Server {

    private ?string $action = "save";

    private static bool $INVALID_UTF8 = true;
    private ?int $flags;

    private DateTime|string|null $date;
    private ?string $input;
    private ?array $get;
    private ?array $post;
    private ?array $content;

    private ?object $obj;
    private string $url = "";
    private string $file = "";
    private string|object|array $data = "";
    private ?array $args;
    private int $status = 200;
    private string|array $error = "";
    private ?Exception $e;
    private ?array $resp;

    public function __construct() {
        $this->date = (new DateTime())->format('Y-m-d H:i:s');
        // $_POST contains the array from x-www-form-urlencoded content, not json.
        $this->input = file_get_contents("php://input");
        $this->get = $_GET;
        $this->post = $_POST;
        $this->content = array("get" => $this->get, "post" => $this->post, "input" => $this->input);
        $this->setFlags();
        $this->parseRequest();
		$action = $this->action."Action";
		$this->$action();
	}

    private function setFlags(): int {
        return $this->flags = self::$INVALID_UTF8 ? (JSON_PRETTY_PRINT | JSON_INVALID_UTF8_SUBSTITUTE) : JSON_PRETTY_PRINT;
	}

    private function parseRequest(): void {
		try {
            $this->obj = json_decode($this->input);
            if (gettype($this->obj) === "object") {
                $this->url = $this->obj->url ?? '';
                $this->file = $this->obj->file ?? '';
                $this->data = $this->obj->data ?? '';
                $this->args = $this->obj->args ?? [];
                if ($this->obj->flags ?? null) {
                    self::$INVALID_UTF8 = $this->obj->flags->INVALID_UTF8;
                    $this->setFlags();
				}
                if ($this->obj->action ?? null) $this->action = $this->obj->action;
            } else {
		        throw new Exception('Не удалось декодировать JSON!');
	        }
            $this->setResp();
        }
        catch (Exception $e) {
            $this->e = $e;
            $this->status = 403;
            $this->error = array("msg" => $e->getMessage(), "e" => $e);
            $this->setResp();
            exit(json_encode($this->resp));
        }

        return;
	}

    private function setResp(): array {
        return $this->resp = array("status" => $this->status, "error" => $this->error, "INVALID_UTF8" => self::$INVALID_UTF8,"flags" => $this->flags, "content" => $this->content);
	}

    private function saveAction(): void {
        $data = array("update" => $this->date, "data" => $this->data);
        $json = json_encode($data, $this->flags);

        $this->dirExist(dirname($this->file));
        file_put_contents($this->file, $json);
		exit(json_encode(array("file" => file_get_contents($this->file), "resp" => $this->resp), $this->flags));
	}

    private function saveImgAction(): void {
        $this->dirExist(dirname($this->file));
		$check_save = file_put_contents($this->file, base64_decode($this->data));
        exit(json_encode($check_save ? array("img" => $this->file) : array("error" => "Ошибка в сохранении файла"), $this->flags));
	}

    private function getPageAction(): void {
        $args = $this->args ? $this->args : [
		    "url" => $this->url
	    ];
		$html = Parser::getPage($args);
        exit(json_encode(array("args" => $this->args, "url" => $this->url, "html" => $html, "resp" => $this->resp), $this->flags));
	}

    private function dirExist($path, $create = true) {
        // Для создания вложенной структуры необходимо указать параметр $recursive в mkdir().
        return is_dir($path) || ($create && mkdir($path, 0777, true));
	}

    private function testInvalidChar(): string {
        /* This generates an invalid character. */
		$invalidChar = chr(193);
		$array = array("Key 1" => 'A', "Key 2" => 'B', "Key 3" => $invalidChar);
		/* The JSON string created from the array, using the JSON_PRETTY_PRINT option. */
		$json = json_encode($array, $this->flags);

		//echo '<pre>';
		echo $json;
		//echo '</pre>';

        return $json;
	}

    private function testStrKey(): string {
        $json = '{"foo-bar": 12345}';
		$obj = json_decode($json);
        return $obj->{'foo-bar'}; // 12345
	}

}

new Server();

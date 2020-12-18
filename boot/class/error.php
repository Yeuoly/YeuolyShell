<?php

namespace YeuolyShellError;

class Error{
    public function __construct($code = 500, $message)
    {
        echo '<strong>ERROR-CODE:' . $code;
        echo '<br/>ERROR-MSG:' . $message;
        ?>
            </strong>
            <br/>
            <h3>感谢您使用YeuolyShell，联系我们：admin@srmxy.cn
        <?php
        header('HTTP/1.1 ' . $code);
    }
}
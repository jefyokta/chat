<?php

use oktaa\model\UserModel;
use Swoole\Coroutine;
use Swoole\Coroutine\Http\Client;

function preprocessTemplate($template)
{
    $res = preg_replace_callback(
        '/<x\s+(.*?)\s+x>/',
        function ($matches) {
            $varName = $matches[1];
            return "<?php echo htmlspecialchars($varName, ENT_QUOTES, 'UTF-8'); ?>";
        },
        $template
    );
    return $res;
    // var_dump($res);

}

class Test
{
    public function run()
    {
        $userid =1;
        $users =  UserModel::raw(
            "SELECT users.id, users.username, MAX(messages.created_at) AS created_at
        FROM users
        INNER JOIN messages ON (users.id = messages.from OR users.id = messages.to)
            AND (messages.from = ? OR messages.to = ?)
        WHERE users.id != ?
        GROUP BY users.id, users.username
        ORDER BY created_at DESC",
        [
            $userid,
            $userid,
            $userid
        ]
        )->get();
        echo json_encode($users,JSON_PRETTY_PRINT);
    }
}

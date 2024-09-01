<?php

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
        function rangeGenerator($start, $end) {
            for ($i = $start; $i <= $end; $i++) {
                yield $i;
            }
        }
        var_dump(rangeGenerator(1,6));
        
        
        // foreach (rangeGenerator(1, 5) as $number) {
        //     echo $number . ' '; // Output: 1 2 3 4 5
        // }
    }
}

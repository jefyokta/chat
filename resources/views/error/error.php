<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/php.min.js"></script>

<div class="p-10 bg-slate-950/70 backdrop-blur rounded-md w-10/12 absolute h-5/6" style="top: 50%; left: 50%; transform: translate(-50%, -50%);">
    <h1 class="text-center text-4xl my-5 font-bold text-slate-200">Error!</h1>
    <div class="p-2 bg-slate-900/50 backdrop-blur rounded-md">
        <p class="text-red-400 text-xl font-bold"><?= $message; ?></p>
    </div>
    <div class="my-5">
        <pre class=" my-5 text-red-200 bg-slate-950/10 backdrop-blur p-2 rounded-md w-full flex">
            <span class="font-bold text-red-500"><?= $file; ?></span> 
            <span class=" text-yellow-500 font-semibold "> On Line : <?= $line; ?></span>
        </pre>
        <pre><code class="language-php rounded-md backdrop-blur shadow-lg"><?= $errorline; ?></code></pre>
    </div>
    <div class="w-full my-3 bg-slate-950/10 text-slate-300 rounded-lg backdrop-blur p-5 flex items-center flex-col ">
        <h1 class="text-xl text-cyan-500 text-center">Powered By Okta</h1>
        <table class="w-8/12 px-3 text-center mt-10">
            <tr>
                <td>Request Uri</td>
                <td><?= $req->server['request_uri']; ?></td>
            </tr>
            <tr>
                <td>remote_addr</td>
                <td><?= ($req->server['remote_addr']);
                    ?></td>
            </tr>
            <tr>
                <td>Method</td>
                <td><?= ($req->server['request_method']);
                    ?></td>
            </tr>
            <tr>
                <td>Request Time</td>
                <td><?= ($req->server['request_time']);
                    ?></td>
            </tr>
        </table>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        hljs.highlightAll();
        var link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.css';
        document.head.appendChild(link);
    });
</script>
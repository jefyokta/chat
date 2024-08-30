<form class="max-w-sm mx-auto mt-10" method="post" action="/login">
    <div class="mb-5">
        <label for="username" class="block mb-2 text-sm font-medium text-gray-900">Your username</label>
        <input type="text" id="username" name="username" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-" placeholder="username" required />
    </div>
    <div class="mb-5">
        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 ">Your password</label>
        <input type="password" name="password" id="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 " placeholder="password" required />
    </div>
    <div class="flex items-start mb-5">
        <div class="flex items-center h-5">

        </div>
        <button type="button" onclick="Register()" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">register</button>
        </div>
  </div>
  <div class="flex justify-end">
    <a href="/login" class="text-blue-800 underline">Login</a>
  </div>
</form>
<script>
    const Register = async () => {

        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;

        const result = await fetch(`/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                password: password
            })
        });

        // const data = await result.json();
        console.log(result)

        if (result.ok) {
            alert("berhasil login")
            location.href = "/login"
        } else {
            alert("username sudah dipakai")
            console.error('Error:', data);
        }

    }
</script>
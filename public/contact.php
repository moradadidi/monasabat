<?php
include_once("../includes/navbar.php");
include_once("../includes/database.php");

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}
?>
<style>
     main {
            background-color: #ffffff;
            background-image: radial-gradient(at 12% 45%, #32CD32 40%, transparent 20%),
                radial-gradient(at 62% 33%, #ff7a00 50%, transparent 50%);
        }
</style>
<body>
    
    <div class="home">
        <main class="py-5  ">
    <div class="max-w-screen-xl mx-auto px-6 text-gray-600 md:px-10">
        <div class="max-w-xl mx-auto space-y-6 sm:text-center">
            <h1 class="text-indigo-600 font-semibold text-7xl text-orange-500">
                Contact
            </h1>
            <p class="text-gray-800 text-5xl font-semibold sm:text-6xl">
                Get in touch
            </p>
            <p class="text-lg">
                We’d love to hear from you! Please fill out the form below.
            </p>
        </div>
        <div class="mt-16 max-w-xl mx-auto">
            <form class="space-y-8">
                <div class="flex flex-col items-center gap-y-8 gap-x-8 [&>*]:w-full sm:flex-row">
                    <div>
                        <label class="font-medium text-lg">
                            First name
                        </label>
                        <input
                            type="text"
                            required
                            class="w-full mt-3 px-4 py-3 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg text-lg"
                        />
                    </div>
                    <div>
                        <label class="font-medium text-lg">
                            Last name
                        </label>
                        <input
                            type="text"
                            required
                            class="w-full mt-3 px-4 py-3 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg text-lg"
                        />
                    </div>
                </div>
                <div>
                    <label class="font-medium text-lg">
                        Email
                    </label>
                    <input
                        type="email"
                        required
                        class="w-full mt-3 px-4 py-3 text-gray-500 bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg text-lg"
                    />
                </div>
                <div>
                    <label class="font-medium text-lg">
                        Phone number
                    </label>
                    <div class="relative mt-3">
                        <div class="absolute inset-y-0 left-4 my-auto h-6 flex items-center border-r pr-3">
                            <select class="text-md bg-transparent outline-none rounded-lg h-full">
                                <option>US</option>
                                <option>ES</option>
                                <option>MAR</option>
                            </select>
                        </div>
                        <input
                            type="number"
                            placeholder="+1 (212) 000-000"
                            required
                            class="w-full pl-[5rem] pr-4 py-3 appearance-none bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg text-lg"
                        />
                    </div>
                </div>
                <div>
                    <label class="font-medium text-lg">
                        Message
                    </label>
                    <textarea required class="w-full mt-3 h-48 px-4 py-3 resize-none appearance-none bg-transparent outline-none border focus:border-indigo-600 shadow-sm rounded-lg text-lg"></textarea>
                </div>
                <button class="w-full px-6 py-3 text-white font-medium bg-orange-500 hover:bg-indigo-500 active:bg-indigo-600 rounded-lg duration-150 text-lg">
                    Send
                </button>
            </form>
        </div>
    </div>
</main>
</div>

            </body>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IT Asset Tracker</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center font-sans antialiased">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-sm sm:rounded-xl border border-slate-100 sm:px-10">
            <h2 class="mt-2 text-center text-2xl font-bold tracking-tight text-slate-900">Initial Setup</h2>
            <p class="mt-2 text-center text-sm text-slate-500 mb-8">Register the first administrative account.</p>

            <form action="{{ route('register') }}" method="POST" class="space-y-6">
                @csrf
                
                @if ($errors->any())
                    <div class="rounded-md bg-rose-50 p-4 border border-rose-200">
                        <ul class="list-disc space-y-1 pl-5 text-sm font-medium text-rose-800">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label for="full_name" class="block text-sm font-medium leading-6 text-slate-900">Full Name</label>
                    <div class="mt-2">
                        <input id="full_name" name="full_name" type="text" required value="{{ old('full_name') }}"
                            class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <label for="department" class="block text-sm font-medium leading-6 text-slate-900">Department</label>
                    <div class="mt-2">
                        <input id="department" name="department" type="text" value="{{ old('department', 'IT') }}"
                            class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium leading-6 text-slate-900">Email address</label>
                    <div class="mt-2">
                        <input id="email" name="email" type="email" required value="{{ old('email') }}"
                            class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium leading-6 text-slate-900">Password</label>
                    <div class="mt-2">
                        <input id="password" name="password" type="password" required
                            class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>
                
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium leading-6 text-slate-900">Confirm Password</label>
                    <div class="mt-2">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            class="block w-full rounded-md border-0 py-1.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

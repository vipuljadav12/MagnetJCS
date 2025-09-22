<pre>
Hello {{$user->first_name}},

your credentials for {{env("APP_NAME")}}

User Name: {{$user->email}}
Password : {{$user->plain_password}}

Regards,
{{env("APP_NAME")}}
</pre>
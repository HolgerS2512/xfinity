<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forget Password</title>
</head>
<body>
    <h2>
        Click here and enter the code to complete the process.: <button style="background:#181818;"> 
            <a
                style="color: white;text-decoration:none;font-size:18px;padding:16px;outline:none;border-radius: 12px;"
                href="http://localhost:3000/resetpassword/{{ $url }}" 
                rel="noopener noreferrer" 
                target="_blank"
            >Password reset</a>
        </button>
        <br />
        <br />
        Pincode: {{ $token }}
    </h2>
</body>
</html>

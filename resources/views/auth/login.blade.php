<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Apriori</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-card {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            padding: 40px;
        }
        .login-card h2 {
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }
        .login-card p {
            color: #777;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 1px solid #ddd;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }
        .btn-primary {
            background: #667eea;
            border: none;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .auth-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: #666;
        }
        .auth-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .icon-box {
            width: 60px;
            height: 60px;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="icon-box">
            <i class="ti ti-lock"></i>
        </div>
        <h2>Masuk Sistem</h2>
        <p>Silakan login untuk mengelola data Apriori</p>

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold">Alamat Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-2"><i class="ti ti-mail"></i></span>
                    <input type="email" name="email" class="form-control border-start-0" placeholder="admin@example.com" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Kata Sandi</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 rounded-start-2"><i class="ti ti-key"></i></span>
                    <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">Ingat Saya</label>
                </div>
                <a href="#" class="small text-decoration-none text-secondary">Lupa Password?</a>
            </div>
            <button type="submit" class="btn btn-primary">Login Sekarang</button>
        </form>

        <div class="auth-footer">
            Sistem Analisis Pola Pembelian Produk (Apriori)
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            @if(session('success'))
                Swal.fire({ title: 'Berhasil!', text: "{{ session('success') }}", icon: 'success' });
            @endif
            @if(session('error'))
                Swal.fire({ title: 'Gagal!', text: "{{ session('error') }}", icon: 'error' });
            @endif
        });
    </script>
</body>
</html>

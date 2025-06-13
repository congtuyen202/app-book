<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

final class AuthMutator // Đổi tên lớp để tránh xung đột với facade Auth
{
    /**
     * Xử lý đăng nhập người dùng.
     *
     * @param  null  $_
     * @param  array  $args
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login($_, array $args): array
    {
        $credentials = [
            'email' => $args['email'],
            'password' => $args['password'],
        ];

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin đăng nhập không hợp lệ.'], // Thông báo lỗi rõ ràng hơn
            ]);
        }

        /** @var \App\Models\User $user */ // Gợi ý kiểu cho $user
        $user = Auth::user();

        // Bạn có thể giữ lại dòng này nếu muốn xóa tất cả các token cũ của người dùng
        // trước khi tạo một token mới. Điều này hữu ích để quản lý số lượng token.
        // $user->tokens()->delete();

        // Tạo một token API mới cho người dùng đã xác thực
        $token = $user->createToken('authToken')->plainTextToken;

        return [
            'token' => $token,
            'user' => $user,
        ];
    }

    /**
     * Xử lý đăng xuất người dùng.
     *
     * @param  null  $_
     * @param  array  $args
     * @return string
     */
    public function logout($_, array $args): string
    {
        // Đảm bảo rằng người dùng đã đăng nhập trước khi cố gắng xóa token
        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $user->currentAccessToken()->delete(); // Xóa token hiện tại của người dùng
            return 'Đã đăng xuất thành công.';
        }

        // Trường hợp người dùng không được xác thực, có thể trả về thông báo lỗi hoặc bỏ qua
        throw ValidationException::withMessages([
            'message' => ['Bạn chưa đăng nhập.'],
        ]);
    }
}
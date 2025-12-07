    <?php

    use App\Http\Controllers\Controller;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Hash;

    class ProfileResepsionisController extends Controller
    {
        public function index()
        {
            $user = Auth::user();
            return view('resepsionis.profile.index', compact('user'));
        }

        public function edit()
        {
            $user = Auth::user();
            return view('resepsionis.profile.edit', compact('user'));
        }

        public function update(Request $request)
        {
            $user = Auth::user();

            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email',
                'password' => 'nullable|min:6'
            ]);

            // Update name & email
            $user->name  = $request->name;
            $user->email = $request->email;

            // Update password jika diisi
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return redirect()->route('resepsionis.profile')
                ->with('success', 'Profile updated successfully.');
        }
    }

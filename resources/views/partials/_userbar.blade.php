<div class="top">
    <button type="button" id="menu-btn">
        <span class="material-icons-sharp">menu</span>
    </button>
    <div class="profile">
        <div class="info">
            @auth
                <p>Welcome, <b>{{ $user->name }}</b></p>
                <small class="text-muted">{{ $user->department }}</small>
            @endauth
        </div>
        <div class="profile-photo">
            <img class="primary"
                src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : asset('images/aclc tacloban.png') }}"alt="logo">
        </div>
    </div>
</div>

<x-admin-layout>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card card-primary card-outline mb-4">
                        @include('admin.profile.partials.update-profile-information-form')
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card card-primary card-outline mb-4">
                        @include('admin.profile.partials.update-password-form')
                    </div>
                </div>
                <!-- <div class="col-md-6">
                    <div class="card card-primary card-outline mb-4">
                        @include('admin.profile.partials.delete-user-form')
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</x-admin-layout>
<!DOCTYPE html>
<html>
<head>
    <title>Groups Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Groups Management</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Referral Code</th>
                                    <th>Description</th>
                                    <th>UKM</th>
                                    <th>Active</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($groups as $group)
                                    <tr>
                                        <td>{{ $group->id }}</td>
                                        <td>{{ $group->name }}</td>
                                        <td>{{ $group->referral_code }}</td>
                                        <td>{{ Str::limit($group->description, 50) }}</td>
                                        <td>{{ $group->ukm ? $group->ukm->name : 'No UKM' }}</td>
                                        <td>{{ $group->is_active ? 'Yes' : 'No' }}</td>
                                        <td>{{ $group->created_at->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No groups found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($groups, 'links'))
                        {{ $groups->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>

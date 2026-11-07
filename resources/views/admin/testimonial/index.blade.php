@extends('admin.layouts.app')

@section('testimonials', 'active')

@section('content')


<div class="card content-card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h2 class="card-title mb-0">Testimonials</h2>

        <a href="{{ route('admin.testimonial.create') }}"
           class="btn btn-primary btn-sm">
            + Add New
        </a>
    </div>

    <div class="card-body p-0">

        @if($testimonials->count())

            <div class="table-responsive p-3">
                <table class="table table-hover mb-0" id="dataTables">

                    <thead>
                        <tr>
                            <th width="5%">SN</th>
                            <th width="10%">Image</th>
                            <th width="10%">Name</th>
                            <th width="15%">Message </th>
                            <th width="5%">Status</th>
                            <th width="5%">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($testimonials as $item)
                            <tr>

                                <td>
                                    {{ $loop->iteration }}
                                </td>
                                <td>
                                    <img src="{{ asset($item->image ?? 'assets/default.png') }}"
                                         width="45" height="45"
                                         style="border-radius:50%">
                                </td>

                                <td>{{ $item->name }}</td>

                                <td>{{ Str::limit($item->message, 100) }}</td>

                                <td>
                                    @if ($item->status == '1')
                                        <span class="text-success">Active</span>
                                    @else
                                        <span class="text-danger">Inactive</span>
                                    @endif
                                </td>

                                <td class="">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.testimonial.edit', $item->id) }}"
                                        class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>

                                        <a href="{{ route('admin.testimonial.delete', $item->id) }}"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Delete this testimonial?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </a>
                                    </div>
                                    {{-- <a href="{{ route('admin.testimonial.edit', $item->id) }}"
                                       class="btn btn-sm btn-outline-secondary">
                                        Edit
                                    </a>

                                    <a href="{{ route('admin.testimonial.delete', $item->id) }}"
                                       onclick="return confirm('Delete this testimonial?')"
                                       class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </a> --}}

                                </td>

                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        @else
            <div class="p-4 text-center text-muted">
                No testimonials found
            </div>
        @endif

    </div>
</div>

@endsection

@php($row = data_get($data ?? [], 'row'))
<dl class="row mb-0">
    <dt class="col-sm-3">Name</dt>
    <dd class="col-sm-9">{{ $row->name ?? '-' }}</dd>
    <dt class="col-sm-3">Email</dt>
    <dd class="col-sm-9">{{ $row->email ?? '-' }}</dd>
    <dt class="col-sm-3">Phone</dt>
    <dd class="col-sm-9">{{ $row->phone ?? '-' }}</dd>
    <dt class="col-sm-3">Subject</dt>
    <dd class="col-sm-9">{{ $row->subject ?? '-' }}</dd>
    <dt class="col-sm-3">Message</dt>
    <dd class="col-sm-9">{!! nl2br(e($row->message ?? '')) !!}</dd>
</dl>

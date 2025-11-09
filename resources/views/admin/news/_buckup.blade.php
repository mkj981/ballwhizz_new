@extends('adminlte::page')

@section('title', 'News Management')

@section('content_header')
    <h1>📰 News Management</h1>
@stop

@section('content')
    {{-- ✅ Render Livewire directly --}}
    @livewire('admin.news-table')
@stop

{{-- ✅ Include Livewire scripts/styles manually (independent of AdminLTE sections) --}}
@livewireStyles
@livewireScripts

@push('css')
    {{-- ✅ Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <style>
        .select2-container--bootstrap-5 .select2-selection {
            min-height: calc(1.5em + .5rem + 2px);
            border: 1px solid #ced4da;
            border-radius: .375rem;
            background-color: #fff;
        }
        .select2-container--bootstrap-5 .select2-selection__rendered {
            padding-left: .5rem;
            padding-right: .5rem;
        }
    </style>
@endpush

@push('js')
    {{-- ✅ jQuery + Select2 --}}
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('livewire:load', function () {

            function initSelect2() {
                $('select.select2').each(function () {
                    const $el = $(this);

                    try {
                        if ($el.hasClass('select2-hidden-accessible')) {
                            $el.select2('destroy');
                        }

                        $el.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: $el.data('placeholder') || 'Select...',
                            allowClear: true,
                        });

                        $el.off('change').on('change', function () {
                            const model = $(this).attr('wire:model');
                            if (model) {
                            @this.set(model, $(this).val());
                            }
                        });

                        console.log('✅ Select2 initialized for', $el.attr('wire:model'));

                    } catch (err) {
                        console.error('⚠️ Select2 init failed', err);
                    }
                });
            }

            initSelect2();
            Livewire.hook('message.processed', initSelect2);
            Livewire.on('refreshSelect2', initSelect2);
        });
    </script>
@endpush

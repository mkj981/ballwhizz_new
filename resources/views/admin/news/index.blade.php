@extends('adminlte::page')

{{-- Enable AdminLTE Select2 plugin (loads jQuery + select2 for you) --}}
@section('plugins.Select2', true)

@section('title', 'News Management')

@section('content_header')
    <h1>📰 News Management</h1>
@stop

@section('content')
    <livewire:admin.news-table />
@stop

@push('css')
    {{-- Optional: Bootstrap 5 theme polish for Select2 --}}
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
    <script>
        /**
         * Initialize Select2 on all .select2 inside Livewire components,
         * always binding $set to the *correct* component using Livewire.find(...).
         */
        function initSelect2Everywhere() {
            if (!window.$ || !$.fn || !$.fn.select2) return;

            document.querySelectorAll('select.select2').forEach((el) => {
                const $el = $(el);

                // Destroy any previous instance
                if ($el.data('select2')) {
                    $el.select2('destroy');
                }

                // Initialize
                $el.select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: $el.data('placeholder') || 'Select...',
                    allowClear: true,
                });

                // Find the nearest Livewire component to this select
                const compRoot = el.closest('[wire\\:id]');
                if (!compRoot) return;

                const compId = compRoot.getAttribute('wire:id');
                const lwComp = Livewire.find(compId);
                if (!lwComp) return;

                // Sync changes to Livewire model
                $el.off('change.select2lw').on('change.select2lw', function () {
                    const model = el.getAttribute('wire:model');
                    if (model) {
                        lwComp.$set(model, $(this).val());
                    }
                });
            });
        }

        document.addEventListener('livewire:init', () => {
            // First paint
            initSelect2Everywhere();

            // Re-run after every DOM morph
            Livewire.hook('morph.updated', () => {
                initSelect2Everywhere();
            });

            // Optional: manual refresh from PHP with $this->dispatch('refreshSelect2')
            Livewire.on('refreshSelect2', () => initSelect2Everywhere());
        });
    </script>
@endpush

@section('css')
    <style>
        .upload-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .img-preview {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        }

        .placeholder-box {
            width: 60px;
            height: 60px;
            border: 1px dashed #ccc;
            border-radius: 8px;
            color: #888;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
        }

        .custom-upload-btn {
            display: inline-block;
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
            color: #333;
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .custom-upload-btn:hover {
            background-color: #e9ecef;
            border-color: #adb5bd;
        }

        .custom-upload-btn i {
            font-size: 11px;
        }

        .card-header {
            border-bottom: 2px solid #f5c542 !important; /* golden accent line */
        }

        .search-box {
            width: 260px;
            position: relative;
        }

        .search-icon {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #888;
            font-size: 13px;
        }

        .search-box input {
            border: none;
            border-radius: 6px;
            font-size: 13px;
            padding: 5px 10px;
            padding-left: 28px !important; /* space for icon */
            background-color: #f8f9fa;
            transition: all 0.2s ease-in-out;
        }

        .search-box input:focus {
            background-color: #fff;
            border: 1px solid #f5c542;
            box-shadow: 0 0 0 0.2rem rgba(245, 197, 66, 0.25);
            outline: none;
        }
        .card-title-spaced {
            margin-right: 2rem !important;
        }
    </style>
@stop

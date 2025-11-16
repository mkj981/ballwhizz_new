<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\AppNews;

class AppNewsTable extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public $showForm = false;
    public $editingId = null;

    public $short_text_en, $short_text_ar, $long_text_en, $long_text_ar, $video_url;

    public $images = [];           // existing images
    public $uploadedImages = [];   // new uploads

    public function render()
    {
        $records = AppNews::when($this->search, fn($q) =>
        $q->where('short_text_en', 'like', "%{$this->search}%")
            ->orWhere('short_text_ar', 'like', "%{$this->search}%")
        )
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.app-news-table', compact('records'));
    }

    /* -----------------------------------------------------------
        CREATE NEW
    ----------------------------------------------------------- */
    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    /* -----------------------------------------------------------
        EDIT
    ----------------------------------------------------------- */
    public function edit($id)
    {
        $news = AppNews::findOrFail($id);

        $this->editingId     = $news->id;
        $this->short_text_en = $news->short_text_en;
        $this->short_text_ar = $news->short_text_ar;
        $this->long_text_en  = $news->long_text_en;
        $this->long_text_ar  = $news->long_text_ar;
        $this->video_url     = $news->video_url;
        $this->images        = $news->images ?? [];

        $this->showForm = true;
    }

    /* -----------------------------------------------------------
        REMOVE SINGLE IMAGE (THIS IS THE METHOD LIVEWIRE NEEDS)
    ----------------------------------------------------------- */
    public function removeImage($index)
    {
        if (isset($this->images[$index])) {
            unset($this->images[$index]);
            $this->images = array_values($this->images); // reindex
        }
    }

    /* -----------------------------------------------------------
        SAVE (CREATE OR UPDATE)
    ----------------------------------------------------------- */
    public function save()
    {
        $data = [
            'short_text_en' => $this->short_text_en,
            'short_text_ar' => $this->short_text_ar,
            'long_text_en'  => $this->long_text_en,
            'long_text_ar'  => $this->long_text_ar,
            'video_url'     => $this->video_url,
        ];

        // Merge existing + uploaded images
        $existing = $this->images ?? [];

        if (!empty($this->uploadedImages)) {
            $newPaths = [];
            foreach ($this->uploadedImages as $file) {
                $newPaths[] = $file->store('app_news_images', 'public');
            }
            $data['images'] = array_merge($existing, $newPaths);
        } else {
            $data['images'] = $existing;
        }

        AppNews::updateOrCreate(
            ['id' => $this->editingId],
            $data
        );

        session()->flash('success', $this->editingId ? 'Updated successfully!' : 'Created successfully!');

        $this->resetForm();
        $this->showForm = false;
    }

    /* -----------------------------------------------------------
        RESET FORM
    ----------------------------------------------------------- */
    public function resetForm()
    {
        $this->reset([
            'editingId',
            'short_text_en',
            'short_text_ar',
            'long_text_en',
            'long_text_ar',
            'video_url',
            'images',
            'uploadedImages'
        ]);
    }

    /* -----------------------------------------------------------
        DELETE ROW
    ----------------------------------------------------------- */
    public function delete($id)
    {
        AppNews::findOrFail($id)->delete();
    }

}

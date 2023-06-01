<?php

namespace App\Traits;

trait CMSContentWithSections {
    public $contentId;

    /**
     * Get content from session
     *
     * @return mixed
     */
    private function getContent() {
        return \Session::get('content_draft_' . $this->contentId);
    }

    /**
     * Set content to session
     *
     * @param $content
     */
    private function setContent($content) {
        \Session::put('content_draft_' . $this->contentId, $content);
    }

    /**
     * Get content sections array
     *
     * @return array
     */
    private function getSections(): array {
        return (array)($this->getContent()->sections ?? []);
    }

    /**
     * Set content settings
     *
     * @param $sections
     */
    private function setSections($sections) {
        // Get current content
        $content = $this->getContent();

        // Update sections
        $content->sections = (object)$sections;

        // Update content
        $this->setContent($content);
    }

    private function emitAndReload($reload = true) {
        // Update parent and enable content save & cancel
        $this->emitTo('livewire.backend.c-m-s.content.content-manager', 'reload'); // Replace with emitUp?
        $this->emit('showEdited');

        // Reload component
        if($reload)
            $this->reload($this->position);
    }
}

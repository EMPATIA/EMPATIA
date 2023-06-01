<?php

namespace App\Http\Controllers\Backend\Empatia\Cbs\Livewire\Cbs;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Helpers\HBackend;
use App\Models\Empatia\Cbs\Cb;
use App\Http\Controllers\Backend\Empatia\Cbs\CbsController;


class CbParameters extends Component
{
    public $cb;
    public $parameterTypes;
    public $hasOptions = false;
    public $editIndex = null;
    public $isShow;

    public $type;
    public $parameterCode;
    public $parameterTitle;
    public $parameterDescription;
    public $parameterRules;
    public $parameterData;
    public $isMandatory;
    public $options;
    public $list;
    public $cbid;
    public $flag = 0;


    protected $rules = [
        'options.*' => 'required',
    ];

    public $messages;

    protected $listeners = [
        'resetModal',
        'destroy',
        'parametersMoved'
    ];

    public function mount() {
        foreach (getLanguages() as $lang) {
            $this->rules['parameterTitle.'.$lang['locale']] = 'required';
        }
        [$this->rules, $this->messages] = HBackend::createControllerValidate($this->rules, 'cbs::cbs.parameters');
        $this->parameterTypes = [
            'label' => __('cbs::cbs.parameters.label.label'),
            'text' => __('cbs::cbs.parameters.label.text'),
            'date' => __('cbs::cbs.parameters.label.date'),
            'textarea' => __('cbs::cbs.parameters.label.textarea'),
            'select' => __('cbs::cbs.parameters.label.select'),
            'radiobutton' => __('cbs::cbs.parameters.label.radiobutton'),
            'checkbox' => __('cbs::cbs.parameters.label.checkbox'),
            'json' => __('cbs::cbs.parameters.label.json'),
            'image' => __('cbs::cbs.parameters.label.image'),
            'file' => __('cbs::cbs.parameters.label.file'),
        ];

        $cbs = Cb::whereId($this->cb->id)->firstOrFail();
        $json_to_array = json_decode(json_encode($this->cb->parameters), true);
        foreach ($json_to_array ?? [] as $key => $value) {
            if ((in_array('position', $value)) == true) {
                $this->flag = 1;
                break;
            } else if ((in_array('position', $value)) == false) {
                $json_to_array[$key]['position'] = $key;
            }
        }

        if ($this->flag == 0) {
            $cbs->update(['parameters' => $json_to_array]);
        }

        $this->reload();
        $this->editIndex = null;
        $this->resetModal();
    }

    public function reload() {
        $this->cb = Cb::findOrFail($this->cb->id);
        $this->dispatchBrowserEvent('reloadScripts');
    }

    public function render()
    {
        return view('empatia::cbs.livewire.cbs.cb-parameters');
    }

    public function updated($fields) {
        $this->validateOnly($fields, $this->rules, $this->messages);
    }

    public function updatedType() {
        $this->hasOptions($this->type);
    }

    public function create() {
        $this->isShow = false;
        $this->resetModal();
        $this->dispatchBrowserEvent('openParametersModal');
    }

    public function show($index) {
        $this->isShow = true;
        $parameter = $this->cb->parameters[$index];
        $this->setModal($parameter);
        $this->dispatchBrowserEvent('openParametersModal');
    }

    public function store() {
        $this->validate($this->rules, $this->messages);
        try {
            CbsController::saveNewVersion($this->cb->id);
            $options = [];
            if ($this->hasOptions && !empty($this->options)) {
                foreach ($this->options as $key => $op) {
                    $options[] = (object)[
                        'id' => $key + 1,
                        'value' => (object) $op,
                    ];
                }
            }

            $parameters = array_values((array)$this->cb->parameters);
            $parameters[] = [
                'id' => count($parameters) > 0 ? collect($parameters)->max('id') + 1 : 1,
                'type' => $this->type,
                'code' => $this->parameterCode,
                'title' => (object)$this->parameterTitle,
                'description' => (object)$this->parameterDescription,
                'rules' => $this->parameterRules,
                'data' => json_decode($this->parameterData,true) ?? $this->parameterData,
                'mandatory' => $this->isMandatory,
                'options' =>$options,
            ];
            DB::beginTransaction();
            $this->cb->update([
                'parameters' => $parameters
            ]);
            DB::commit();
            $this->dispatchBrowserEvent('closeParametersModals');
        }catch (\Exception $e) {
            DB::rollBack();
            logError('error creating parameter: '. $e->getMessage());
        }
    }

    public function edit($index) {
        $this->isShow = false;
        $this->editIndex = $index;
        $parameter = $this->cb->parameters[$index];
        $this->setModal($parameter);
        $this->dispatchBrowserEvent('openParametersModal');
    }

    public function update() {
        $this->validate($this->rules, $this->messages);
        try {
            CbsController::saveNewVersion($this->cb->id);
            $options = [];
            if ($this->hasOptions && !empty($this->options)) {
                foreach ($this->options as $key => $op) {
                    $options[] = (object)[
                        'id' => $key + 1,
                        'value' => (object) $op,
                    ];
                }
            }
            $parameters = array_values((array)$this->cb->parameters);
            $parameters[$this->editIndex] = [
                'id' => $parameters[$this->editIndex]->id,
                'type' => $this->type,
                'code' => $this->parameterCode,
                'title' => (object)$this->parameterTitle,
                'description' => (object)$this->parameterDescription,
                'rules' => $this->parameterRules,
                'data' => $this->parameterData != null ? json_decode($this->parameterData) : null,
                'mandatory' => $this->isMandatory,
                'options' => $options,
            ];
            DB::beginTransaction();

            $this->cb->update([
                'parameters' => $parameters
            ]);
            DB::commit();
            $this->dispatchBrowserEvent('closeParametersModals');
        } catch (\Exception $e) {
            DB::rollBack();
            logError('error updating parameters: '. $e->getMessage());
            dd($e);
        }
    }

    public function destroy($index) {
        try {
            $parameters = $this->cb->parameters;
            unset($parameters[$index]);
            $this->cb->update([
                'parameters' => $parameters
            ]);
            $this->reload();
        } catch (\Exception $e) {
            logError('deleting param index ' . $index . ': ' . $e);
        }
    }

    private function hasOptions($type): bool
    {
        if (in_array($this->type, ['select', 'checkbox', 'radiobutton'])) {
            $this->hasOptions = true;
            $this->options = [];
            return true;
        }
        return false;
    }

    public function addOption() {
        $this->options[] = null;
    }

    public function removeOption($i) {
        unset($this->options[$i]);
    }

    private function setModal($parameter)
    {
        $this->type = $parameter->type;
        $this->parameterCode = $parameter->code;
        $this->parameterTitle = (array)$parameter->title;
        $this->parameterDescription = (array)$parameter->description;
        $this->parameterRules = getField($parameter, 'rules');
        $this->parameterData = getField($parameter, 'data') ? json_encode($parameter->data) : null;
        $this->isMandatory = getField($parameter, 'mandatory');
        $this->hasOptions($parameter->type);
        if (isset($parameter->options)) {
            foreach ($parameter->options as $option) {
                $this->options[$option->value] = $option->label;
            }
        } else {
            $this->options = [];
        }

    }

    public function resetModal()
    {
        $this->editIndex = null;
        $this->type = $this->parameterCode = $this->parameterRules =
        $this->parameterTitle = $this->parameterDescription =
        $this->parameterData = $this->isMandatory = null;
        $this->hasOptions = false;
        $this->options = [];
    }

    public function parametersMoved($list, $cbId){
        try {
            $cbs = Cb::whereId($cbId)->firstOrFail();
            $json_to_array = json_decode(json_encode($cbs->parameters), true);
            foreach ($list as $pos => $id) {
                $json_to_array[$id]['position'] = $pos;
            }
            $cbs->update(['parameters' => $json_to_array]);
        } catch
        (\Exception $e) {
            logError('update position: ' . $e->getMessage());
        } finally {
            $this->reload();
        }
    }

}

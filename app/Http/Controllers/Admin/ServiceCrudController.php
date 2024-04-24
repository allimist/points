<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ServiceRequest;
use App\Models\Resource;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class ServiceCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ServiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Service::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/service');
        CRUD::setEntityNameStrings('service', 'services');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        CRUD::column('id');
//        CRUD::column('created_at');
        CRUD::column('name');
        CRUD::column('resource');
        CRUD::column('level');
        CRUD::column('cost')->type('string');
        CRUD::column('revenue')->type('string');

        CRUD::column('image_init')->type('image')->prefix('storage/');
        CRUD::column('image_ready')->type('image')->prefix('storage/');
        CRUD::column('image_reload')->type('image')->prefix('storage/');
        CRUD::column('xp');
        CRUD::column('time');
        CRUD::column('reload');
        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']);
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ServiceRequest::class);

        CRUD::field('name');
        CRUD::field('resource');
        CRUD::field('level');

//        $resources = Resource::all();
//        $resourceOptions = [];
//        foreach($resources as $resource){
//            $resourceOptions[$resource->id] = $resource->name;
//        }
        $currencies = \App\Models\Currency::all();
        $currencyOptions = [];
        foreach($currencies as $currency){
            $currencyOptions[$currency->id] = $currency->name;
        }

        //CRUD::field('cost');
        $this->crud->addField([
//            'label' => 'Conditions * AND ( cmp-rpm + 10 ) OR ( cmp-visits - 20 ) - SOON',
            'name' => 'cost',
//            'type'  => 'view',
//            'view' => 'vendor/backpack/crud/fields/repeatable',
            'type'  => 'repeatable',
            'subfields' => [
                [
                    'name'    => 'resource',
                    'type'    => 'select2_from_array',
                    'options'    => $currencyOptions,
                    'wrapper' => ['class' => 'form-group col-md-5'],
                ],
                [
                    'label'   => 'value',
                    'name'    => 'value',
                    'type'    => 'number',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
            ],
            'new_item_label'  => 'Add Group',
            'init_rows' => 0,
            'max_rows' => 5,
        ]);
//        CRUD::field('revenue');
        $this->crud->addField([
//            'label' => 'Conditions * AND ( cmp-rpm + 10 ) OR ( cmp-visits - 20 ) - SOON',
            'name' => 'revenue',
//            'type'  => 'view',
//            'view' => 'vendor/backpack/crud/fields/repeatable',
            'type'  => 'repeatable',
            'subfields' => [
                [
                    'name'    => 'resource',
                    'type'    => 'select2_from_array',
                    'options'    => $currencyOptions,
                    'wrapper' => ['class' => 'form-group col-md-5'],
                ],
                [
                    'label'   => 'value',
                    'name'    => 'value',
                    'type'    => 'number',
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name'    => 'percent',
                    'type'    => 'number',
//                    'options'    => $currencyOptions,
                    'default' => 100,
                    'wrapper' => ['class' => 'form-group col-md-2'],
                ],
                [
                    'name'    => 'level',
                    'type'    => 'number',
//                    'options'    => $currencyOptions,
                    'default' => 0,
                    'wrapper' => ['class' => 'form-group col-md-3'],
                ],
            ],
            'new_item_label'  => 'Add Group',
            'init_rows' => 0,
            'max_rows' => 5,
        ]);

        CRUD::field('xp');
        CRUD::field('damage');
        CRUD::field('time');
        CRUD::field('reload');
//        CRUD::field('currency');

        $this->crud->addField([
            'name' => 'image_init',
            'label' => 'Image Init',
            'type' => 'image_custom',
            'upload' => true,
        ]);

        $this->crud->addField([
            'name' => 'image_ready',
            'type' => 'image_custom',
            'upload' => true,
        ]);

        $this->crud->addField([
            'name' => 'image_reload',
            'type' => 'image_custom',
            'upload' => true,
        ]);



        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number']));
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /*
    public function store()
    {
//        dd('ok- store');
        $this->crud->hasAccessOrFail('create');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        $select2_fields = ['cost','revenue'];
        foreach ($select2_fields as $select2_field){
            if(empty($request->get($select2_field))){
                $request->offsetSet($select2_field,json_encode([]));
            }
//            dd($request->get($select2_field));
        }

        // insert item in the db
        $item = $this->crud->create($this->crud->getStrippedSaveRequest($request));
        $this->data['entry'] = $this->crud->entry = $item;



        // update the row in the db
//        $item = $this->crud->update(
//            $request->get($this->crud->model->getKeyName()),
//            $this->crud->getStrippedSaveRequest($request)
//        );

        // show a success message
        \Alert::success(trans('backpack::crud.insert_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());

    }

    public function update()
    {
        $this->crud->hasAccessOrFail('update');

        // execute the FormRequest authorization and validation, if one is required
        $request = $this->crud->validateRequest();

        // register any Model Events defined on fields
        $this->crud->registerFieldEvents();

        $select2_fields = ['cost','revenue'];
        foreach ($select2_fields as $select2_field){
            if(empty($request->get($select2_field))){
                $request->offsetSet($select2_field,[]);
            } else {
                $request->offsetSet($select2_field,json_encode($request->get($select2_field)));
            }
        }

        // update the row in the db
        $item = $this->crud->update(
            $request->get($this->crud->model->getKeyName()),
            $this->crud->getStrippedSaveRequest($request)
        );

        $this->data['entry'] = $this->crud->entry = $item;

        // show a success message
        \Alert::success(trans('backpack::crud.update_success'))->flash();

        // save the redirect choice for next time
        $this->crud->setSaveAction();

        return $this->crud->performSaveAction($item->getKey());
    }
    */
}

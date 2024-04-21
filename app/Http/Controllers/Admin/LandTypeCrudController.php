<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\LandTypeRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LandTypeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LandTypeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CloneOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\LandType::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/land-type');
        CRUD::setEntityNameStrings('land type', 'land types');
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
        CRUD::column('name');
        CRUD::column('size');
        CRUD::column('farms')->type('string');
        CRUD::column('image')->type('image')->prefix('storage/');
        CRUD::column('grid')->type('string');


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
        CRUD::setValidation(LandTypeRequest::class);

        CRUD::field('name');
//        CRUD::field('farms');

//        $currencies = \App\Models\Currency::all();
//        $currencyOptions = [];
//        foreach($currencies as $currency){
//            $currencyOptions[$currency->id] = $currency->name;
//        }
        $resourceOptions = \App\Models\Resource::all()->pluck('name', 'id')->toArray();
        CRUD::field('size');

        $this->crud->addField([
//            'label' => 'Conditions * AND ( cmp-rpm + 10 ) OR ( cmp-visits - 20 ) - SOON',
            'name' => 'farms',
//            'type'  => 'view',
//            'view' => 'vendor/backpack/crud/fields/repeatable',
            'type'  => 'repeatable',
            'subfields' => [
                [
                    'name'    => 'resource',
                    'type'    => 'select2_from_array',
                    'options'    => $resourceOptions,
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
            'max_rows' => 20,
        ]);
        $this->crud->addField([
            'name' => 'image', // The db column name where the image path is stored
            'label' => 'Image', // Field label shown on the form
            'type' => 'image_custom',
            'upload' => true,
//            'disk' => 'public', // Optional: Specify the filesystem disk you want to use
//            'prefix' => 'uploads/images/', // Optional: Prefix path where the image will be stored in the disk
//            'label' => 'Image',
//            'name' => 'image',
//            'type' => 'image',
////            'type'      => 'upload',
//            'crop' => true, // set to true to allow cropping, false to disable
////            'upload' => true,
//            'aspect_ratio' => 1, // omit or set to 0 to allow any aspect ratio
//            'disk' => 'public',
//            'rules' => 'required|image|max:5000'
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
}

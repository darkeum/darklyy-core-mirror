<?php

use \Boot\Support\Facades\Router;
use Organizations\Controller\OrganizationsController;
use Organizations\Controller\OrganizationsBankController;
use Organizations\Controller\OrganizationsContactFacesController;
use Organizations\Support\OrganizationsSupport;
use \Boot\System\Http\Request;
use Organizations\Model\Organizations;

Router::mount('/administration/module/organizations', function (){ 
    Router::get('/print/{id}',  [OrganizationsController::class, "print"])->name("organizations.print");
    Router::get('/show_/{id}',  [OrganizationsController::class, "show_"])->name("organizations.show_");
    Router::get('/requisites/{id}',  [OrganizationsController::class, "requisites"])->name("organizations.requisites");
    Router::get('/events/{id}',  [OrganizationsController::class, "events"])->name("organizations.events");
    Router::get('/arbitrages/{id}',  [OrganizationsController::class, "arbitrages"])->name("organizations.arbitrages");
    Router::get('/contracts/{id}',  [OrganizationsController::class, "contracts"])->name("organizations.contracts");
    Router::get('/fssp/{id}',  [OrganizationsController::class, "fssp"])->name("organizations.fssp");
    Router::get('/relations/{id}',  [OrganizationsController::class, "relations"])->name("organizations.relations");
    Router::post('/findByInn/{id}',  [OrganizationsController::class, "findByInn"])->name("organizations.findByInn");
    Router::get('/clear_cache/{id}',  [OrganizationsController::class, "clear_cache"])->name("organizations.clear_cache");
    Router::get('/edit-contracts-list/{id}',  [OrganizationsController::class, "editContractsList"])->name("organizations.editContractsList");
    Router::post("/ajax/org_add", function(Request $request){
        if($request->has("inn")){
            $inn = $request->get("inn");
            $email = $request->has("email") ? $request->get("email") : null;
            $phone = $request->has("phone") ? $request->get("phone") : null;
            try {
                $org = OrganizationsSupport::createOrgByInn($inn);
                $org->phone = $phone;
                $org->email = $email;
                $org->save();
                if($request->has("bik")){
                    $bik = $request->get("bik");
                    $rs = $request->get("rs");
                    if(!empty($bik)){
                        OrganizationsSupport::createBankByBRSO($bik, $rs, $org->id);
                    }
                }
                echo 1;
            } catch (\Exception $e) {
                echo 4;
            }
        } else {
            echo 3;
        }
    });
    
    Router::resource('/bank',  OrganizationsBankController::class)->names("organizations_bank"); 
    Router::resource('/contact_faces',  OrganizationsContactFacesController::class)->names("organizations_contact_faces"); 
    Router::resource('/',  OrganizationsController::class)->names("organizations"); 
}, "admin.");

// dd(Router::dumpAlias());
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

// Support
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
// Helpers
use Illuminate\Support\Str;
// Models
use App\Models\Dish;

// Requests
use App\Http\Requests\StoreDishRequest;
use App\Http\Requests\UpdateDishRequest;

class DishController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $restaurant = $user->restaurant;
        $dishes = Dish::where('restaurant_id',$restaurant->id)
                    ->orderBy('name')
                    ->get();
        // $dishes = $user->restaurant->dishes; DA PROVARE 

        return view('admin.dishes.index', compact('dishes','restaurant'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.dishes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDishRequest $request)
    {
        $validatedDishData = $request->validated();

        $dishImgPath = null;

        if (isset($validatedDishData['img'])) {
            $dishImgPath = Storage::disk('public')->put('images', $validatedDishData['img']);
        }
        //immagine di defaut nulla e apriamo una condizione:"se nelle nostre validation c'è l'immagine,
        //allora salviamo il percorso:disco publico e put(mettila nella cartella images)"

        $validatedDishData['img'] = $dishImgPath;

        $slug = Str::slug($validatedDishData['name']);

        $user = Auth::user();

        $restaurant = $user->restaurant;

        $validatedDishData['restaurant_id'] = $restaurant->id;



        $dish = Dish::create([
            'name' => $validatedDishData['name'],
            'slug' => $slug,
            'description' => $validatedDishData['description'],
            'price' => $validatedDishData['price'],
            'visible' => $validatedDishData['visible'] ?? 0,  // Set default value to 0 if 'visible' is not set
            'restaurant_id' => $validatedDishData['restaurant_id'],
            'img' => $dishImgPath,
        ]);

        return redirect()->route('admin.dishes.show', ['dish' => $dish->slug]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;
        $dish = Dish::where('slug', $slug)->firstOrFail();
        
        if($dish->restaurant_id == $restaurant->id){
            return view('admin.dishes.show', compact('dish'));
        }
        else{
            return back()->withErrors('Piatto non trovato');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;
        $dish = Dish::where('slug', $slug)->firstOrFail();

        if($dish->restaurant_id == $restaurant->id){
            return view('admin.dishes.edit', compact('dish'));
        }
        else{
            return back()->withErrors('Piatto non trovato');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDishRequest $request, string $slug)
    {
        $validatedDishData = $request->validated();
        $dish = Dish::where('slug', $slug)->firstOrFail();

        $dishImgPath = $dish->img;

        if (isset($validatedDishData['img'])) {
            if ($dishImgPath != null) {
                Storage::disk('public')->delete($dish->img);
            }

            $dishImgPath = Storage::disk('public')->put('images', $validatedDishData['img']);
        } else if (isset($validatedDishData['delete_img'])) {
            Storage::disk('public')->delete($dish->img);

            $dishImgPath = null;
        }

        $validatedDishData['img'] = $dishImgPath;

        $slug = str()->slug($validatedDishData['name']);

        $validatedDishData['slug'] = $slug;

        if(!isset($validatedDishData['visible'])){

            $validatedDishData['visible'] = 0;
        }

        $dish->update($validatedDishData);


        return redirect()->route('admin.dishes.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        
        if (Auth::check()) {
            
            $user = Auth::user();
            
            $restaurant = $user->restaurant;
            $dish = Dish::where('slug', $slug)->firstOrFail();
            
            if ($dish->restaurant_id === $restaurant->id) {
                
                $dish->delete();
                
                return redirect()->route('admin.dishes.index')->with('dishDeleted', 'Il piatto è stato eliminato con successo.');
            }
        } else {
            
            return redirect()->route('login')->with('error', 'Devi effettuare l\'accesso per eliminare un piatto.');
        }
    }
}

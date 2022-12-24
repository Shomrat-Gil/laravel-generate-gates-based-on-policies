
# Laravel - Generate Gates based on Policies
 
### You can learn more about Laravel Gates [here](https://laravel.com/docs/9.x/authorization#gates)

  
**This method will generate Gates action name based on Policies**

**For example:**

    Policy class name "SomePolicy" with method name "update" will be defined as Gate: some_update
 
    namespace App\Policies;     
    use App\Models\User;
    use Illuminate\Auth\Access\Response;
    /**
    * Determine if the given post can be updated by the user.
    * @param  \App\Models\User  $user
    * @return \Illuminate\Auth\Access\Response
    */
    public function edit(User $user, int $id){ 
	     $user->load(['some_relationship' => function ($query) use ($id) {
		     $query->where('id', $id);
		 }]);
		 $some = $user->getRelation('some_relationship')->first(); 
	    return $some
                ? Response::allow()
                : Response::deny('Some no access message.');
    }

Calling the Policy:

    $this->authorize('edit', [\App\Models\SomeModel::class, 2]);
    $this->authorizeForUser($user, [\App\Models\SomeModel::class, 2]);

Calling the Policy based on the generated gate: 

    \Illuminate\Support\Facades\Gate::authorize('some_update', 2);
    \Illuminate\Support\Facades\Gate::authorizeForUser($user, 'some_update', 2);


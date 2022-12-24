<?php
namespace App\Providers;

use App\Models\SomeModel;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Policies\SomePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use Illuminate\Database\Eloquent\Model;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        SomeModel::class => SomePolicy::class,
    ];
    /**
     * Register any authentication / authorization services.
     *
     * @return void
     * @throws ReflectionException
     */
    public function boot()
    {
        $this->registerPolicies();
        
        $this->assignGateToPolicy();
    }
    /**
     * This method creates Gates based on Policies
     * For example:
     * Policy class name "FooBooPolicy" with method name "bar" will be defined as Gate: foo_boo_bar
     * Gate call will be: $data = Gate::authorize('foo_boo_bar', $args);
     * @return void
     * @throws ReflectionException
     */
    private function assignGateToPolicy(): void
    {
        //Gate::define Ability used snake format foo_boo.
        //Gate::define Callback format ClassName@methodName.
        foreach ($this->policies() as $policy) {
            $policyName =  Str::of($policy)->replaceLast('Policy', '')->afterLast('\\')->snake();
            $class = new ReflectionClass($policy);
            $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $name = $method->getName();
                if ($name !== '__construct') {
                    $ability = $policyName . '_' . Str::snake($name);
                    $callback = $policy . '@' . $name;
                    Gate::define($ability, $callback);
                }
            }
        }
    }
}

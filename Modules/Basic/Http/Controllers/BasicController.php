<?php

namespace Modules\Basic\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Modules\Basic\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;

class BasicController extends Controller
{
    use ApiResponseTrait;

    /**
     * This function returns a dashboard view with a specified layout and login layout, and additional
     * variables if provided.
     * 
     * param viewName The name of the view file to be rendered.
     * param vars An optional array of variables that can be passed to the view to be rendered. These
     * variables can be used in the view to display dynamic content.
     * 
     * return a view with the specified view name and variables, along with the layout and login
     * layout specified in the configuration file for the active dashboard. If the view does not exist,
     * it returns a 404 error.
     */
    protected function getDashboardView($viewName, $vars = [])
    {
        
        $activeDashboard = config('dashboard.active_dashboard');
        if(Auth::guard('supplier')->check()){
            $activeDashboard=$activeDashboard.'_supplier';
        }

        $view = $viewName;
        $layout = config("dashboard.layouts.$activeDashboard");

        $loginLayout = config("dashboard.login.$activeDashboard");
        
        if (View::exists($view)) {
         
            $viewData = ['layout' => $layout, 'loginLayout' => $loginLayout] + $vars;
            return view($view)->with($viewData);
        }
     
        abort(404, 'Dashboard view not found.');
    }

    /**
     * This function returns the result of calling the "show" method of a service object with the given
     * ID parameter.
     * 
     * param id The parameter "id" is a variable that represents the unique identifier of a resource
     * that needs to be retrieved or displayed. It is commonly used in web applications to retrieve a
     * specific record from a database or to display a specific page or content based on its unique
     * identifier. In the given code snippet, the
     * 
     * return The `show()` method is being called on the `->service` object with the ``
     * parameter, and the result of that method call is being returned. The specific content of the
     * returned value depends on the implementation of the `show()` method in the service class.
     */
    public function show($id)
    {
        return $this->service->show($id);
    }

    /**
     * This PHP function changes the status of a given ID and returns a JSON response indicating
     * whether the status change was successful or not.
     * 
     * param id The ID of the item whose status needs to be changed.
     * 
     * return A JSON response with the status of the changeStatus method of the service class. The
     * status is converted to a string 'true' or 'false' and returned as the value of the 'status' key
     * in the JSON response.
     */
    public function changeStatus($id = null)
    {
        if (is_null($id)) {
            $id = request('id');
        }
        return  response()->json(['status' =>  $this->service->changeStatus($id, 'status')->status ? 'true' : 'false']);
    }

    /**
     * This PHP function returns a JSON response with a message indicating whether a record with the
     * given ID was successfully deleted using a service.
     * 
     * param id The parameter `` is the identifier of the resource that needs to be deleted. It is
     * passed as an argument to the `destroy` method. The method then calls a service to delete the
     * resource with the given identifier and returns a JSON response with a message indicating whether
     * the deletion was successful or not
     * 
     * return A JSON response with a message indicating the result of calling the `delete` method of
     * the service with the given ``.
     */
    public function destroy($id)
    {
        return response()->json(['message' => $this->service->destroy(request(), $id)]);
    }

    /**
     * This function returns a list of items using a service, with pagination enabled.
     * 
     * param request  is an instance of the Request class, which contains the HTTP
     * request information such as the request method, headers, and parameters. It is used to retrieve
     * data from the client-side and pass it to the server-side for processing. In this case, it is
     * being passed as a parameter to the list
     * 
     * return The `list` method of the current class is being called with three arguments: ``,
     * `true`, and `->perPage()`. The method is expected to return something, but without seeing
     * the implementation of the `list` method or the `service` property, it is impossible to determine
     * what exactly is being returned.
     */
    public function list(Request $request)
    {
        return $this->service->list($request, true, $this->perPage());
    }

    /**
     * This PHP function returns the number of items to display per page, defaulting to 10 if not
     * specified in the request.
     * 
     * return The function `perPage()` returns the value of `Request()->perPage` if it is set,
     * otherwise it returns 10.
     */
    public function perPage()
    {
        return !isset(Request()->perPage) ? 10 : Request()->perPage;
    }

    /**
     * This PHP function checks if pagination is set in the request and returns its value.
     * 
     * return a boolean value. If the "pagination" parameter is not set in the request, it will return
     * false. Otherwise, it will return the value of the "pagination" parameter.
     */
    public function pagination()
    {
        return !isset(Request()->pagination) ? false : Request()->pagination;
    }
}

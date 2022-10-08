import React, { useEffect } from 'react';
import ReactDOM from 'react-dom';
import { Provider, useDispatch, useSelector } from 'react-redux';
import { BrowserRouter as Router, Route, Switch } from "react-router-dom";
import Header from './components/Header';
import Categories from './containers/Categories';
import Home from './containers/Home';
import Signin from './containers/SignIn';
import AddProduct from './containers/Products/addProduct';
import PrivateRoute from './components/HOC/PrivateRoute';
import { isUserLoggedIn } from './actions/auth';
import ShowCategories from './containers/Categories/Categories';
import CheckOut from './containers/Checkout';
import Orders from './containers/Orders';
import Users from './containers/Users';
import store from './store';
import Products from './containers/Products';
import TempOrders from './containers/Orders/tempOrders';

function App() {
    const dispatch = useDispatch();
    const auth = useSelector(state => state.auth.authData);

    useEffect(() => {
        if(!auth?.user)
        {
            dispatch(isUserLoggedIn());
        }
            
        // dispatch(getInitialData());
    }, []);

    return (
    <div className="App">
        <Router>           
            <Header />
            <Switch>
                <PrivateRoute path="/" exact component={Home} />
                <PrivateRoute path="/categories" component={Categories} />
                <PrivateRoute path="/show-products" component={Products} />
                <PrivateRoute path="/add-product" component={AddProduct} />
                <PrivateRoute path="/show-categories" component={ShowCategories} />
                <PrivateRoute path="/show-users" component={Users} />
                <PrivateRoute path="/show-orders" component={Orders} />
                <PrivateRoute path="/show-temp-orders" component={TempOrders} />
                <PrivateRoute path="/checkout" component={CheckOut} />

                <Route path="/signin" component={Signin} />
            </Switch>
        </Router>
    </div>
    );
}

let container = document.getElementById('app');
let component = (<Provider store={store}><App/></Provider>);

ReactDOM.render(component, container);
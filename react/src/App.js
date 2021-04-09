import React, { useEffect } from 'react';
import ReactDOM from 'react-dom';
import { Provider, useDispatch, useSelector } from 'react-redux';
import { BrowserRouter as Router, Route, Switch } from "react-router-dom";
import { applyMiddleware, createStore } from 'redux';
import Header from './components/Header';
import Categories from './containers/Categories';
import Home from './containers/Home';
import Signin from './containers/SignIn';
import Signup from './containers/SignUp';
import rootReducer from './reducers';
import thunk from 'redux-thunk';
import AddProduct from './containers/Products/addProduct';
import PrivateRoute from './components/HOC/PrivateRoute';
import { isUserLoggedIn } from './actions/auth';
import ShowCategories from './containers/Categories/Categories';

const store = createStore(rootReducer, applyMiddleware(thunk));

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
                <PrivateRoute path="/add-product" component={AddProduct} />
                <PrivateRoute path="/show-categories" component={ShowCategories} />

                <Route path="/signin" component={Signin} />
                <Route path="/signup" component={Signup} />
            </Switch>
        </Router>
    </div>
    );
}

let container = document.getElementById('app');
let component = (<Provider store={store}><App/></Provider>);

ReactDOM.render(component, container);
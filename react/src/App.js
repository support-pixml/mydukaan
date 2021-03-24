import React from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'react-redux';
import { BrowserRouter as Router, Route, Switch } from "react-router-dom";
import { applyMiddleware, createStore } from 'redux';
import Header from './components/Header';
import Categories from './containers/Categories';
import Home from './containers/Home';
import Signin from './containers/SignIn';
import Signup from './containers/SignUp';
import rootReducer from './reducers';
import thunk from 'redux-thunk';

const store = createStore(rootReducer, applyMiddleware(thunk));

function App() {
    return (
    <div className="App">
        <Router>
            <Header />
            <Switch>
                <Route path="/" exact component={Home} />
                <Route path="/signin" component={Signin} />
                <Route path="/signup" component={Signup} />
                <Route path="/categories" component={Categories} />
            </Switch>
        </Router>
    </div>
    );
}

let container = document.getElementById('app');
let component = (<Provider store={store}><App/></Provider>);

ReactDOM.render(component, container);
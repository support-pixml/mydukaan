import React, { useEffect, useState } from 'react';
import Center from './Center';
import LeftSidebar from './leftSidebar';
import RightSidebar from './rightSidebar';
import SearchBar from './SearchBar';
import { useDispatch, useSelector } from 'react-redux';
import { getCategories } from '../../actions/categories';
import { Container, Grid, Hidden, makeStyles, Snackbar } from '@material-ui/core';
import { getAllProducts, getSearchedProducts } from '../../actions/products';
import { Alert, AlertTitle } from '@material-ui/lab';
import { cartConstants, errorConstants } from '../../actions/constants';
import { Link } from 'react-scroll';

const useStyles = makeStyles((theme) => ({
    alert: {
        position: 'sticky',
        overFlow: 'hidden',
        marginTop: '100px',
        marginBottom: '-80px',
    },    
}));

const Home = () => {
    const dispatch = useDispatch();   
    const classes = useStyles();

    const [messageOpen, setMessageOpen] = useState(true);
    const [errorOpen, setErrorOpen] = useState(true);
    const [search, setSearch] = useState(null);

    useEffect(() => {
        if(search && search != '' && search.length >= 3)
        {           
            dispatch(getSearchedProducts({search}));
        }
        else
        {
            dispatch(getAllProducts());
        }
    }, [search]);

    const cartItems = useSelector((state) => state.cart.cartItems);
    const auth = useSelector((state) => state.auth.authData);
    const cat_products = useSelector((state) => state.product.products); 
    const message = useSelector((state) => state.cart.response); 
    const error = useSelector((state) => state.error.error); 

    const errorCloseHandler = () => {
        setMessageOpen(false);
        setErrorOpen(false);
        dispatch({
            type: cartConstants.CLEAR
        });
        dispatch({
            type: errorConstants.ERROR_CLEAR
        });
    }

    let today = new Date();
    today = `${today.getFullYear()}-${today.getMonth()+1}-${today.getDate()}`;

    useEffect(() => {
        dispatch(getCategories());
        dispatch(getAllProducts());
    }, []);

    return (
        <Container className="">
            <Grid container justify="center">    
                <Grid item md={6} xs={12}>
                    {customer && new Date(customer.expiry_date).getTime() < new Date().getTime() &&
                    <Alert severity="warning" className={classes.alert}>
                        <AlertTitle>Warning</AlertTitle>
                        Your account has been expired. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                    </Alert>}
                    {customer && new Date(customer.expiry_date).getFullYear() == new Date().getFullYear() && new Date(customer.expiry_date).getMonth() == new Date().getMonth() && new Date(customer.expiry_date).getDate() == new Date().getDate()+2 &&
                    <Alert severity="warning" className={classes.alert}>
                        <AlertTitle>Warning</AlertTitle>
                        Your account will be going to expire in 2 days. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                    </Alert>}
                    {customer && new Date(customer.expiry_date).getFullYear() == new Date().getFullYear() && new Date(customer.expiry_date).getMonth() == new Date().getMonth() && new Date(customer.expiry_date).getDate() == new Date().getDate()+1 &&
                    <Alert severity="warning" className={classes.alert}>
                        <AlertTitle>Warning</AlertTitle>
                        Your account will be going to expire tomorrow. — <strong>Your Expiry Date: {customer.expiry_date}</strong>
                    </Alert>}
                    <SearchBar setSearch={setSearch} />
                </Grid>
            </Grid>
            <Grid container spacing={2}>
                <Hidden only="xs">
                    <Grid item md={3} style={{borderRight: '1px solid #666'}}>
                        <LeftSidebar Link={Link} />
                    </Grid>
                </Hidden>
                <Grid item md={6} xs={12}>
                    <Center cartItems={cartItems} auth={auth} cat_products={cat_products} />
                    {message && <Snackbar open={messageOpen} autoHideDuration={3000} onClose={errorCloseHandler}>
                        <Alert severity="success">{message.message}</Alert>
                    </Snackbar>}
                    {error && <Snackbar open={errorOpen} autoHideDuration={3000} onClose={errorCloseHandler}>
                        <Alert severity="error">{error.split(',').map((message, index) => {
                            return (<>{message}<br/></>);
                        })}</Alert>
                    </Snackbar>}
                </Grid>
                <Hidden only="xs">
                    <Grid item md={3} style={{borderLeft: '1px solid #666'}}>
                        <RightSidebar auth={auth} />
                    </Grid>
                </Hidden>
            </Grid>
        </Container>
    )
}

export default Home;
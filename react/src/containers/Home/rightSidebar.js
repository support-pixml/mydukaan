import { Button, Grid, makeStyles, Typography } from '@material-ui/core';
import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Link } from 'react-router-dom';
import { removeProduct } from '../../actions/cart';

const useStyles = makeStyles((theme) => ({
    root: {
        flexGrow: 1,
        maxWidth: 752,
        // position: -webkit-sticky,
        position: 'sticky',
        height: '100%',
        top: '90px',
        maxHeight: '-webkit-calc(100vh - 90px)',
        maxHeight: 'calc(100vh - 90px)',
        overFlow: 'hidden',
    },
    demo: {
        backgroundColor: theme.palette.background.default,
    },
}));

const rightSidebar = () => {

    const classes = useStyles();
    const cartItems = useSelector((state) => state.cart.cartItems);
    console.log(cartItems);
    const cartTotal = useSelector((state) => state.cart.total);
    const dispatch = useDispatch();

    const handleRemoveProduct = (item) => {
        dispatch(removeProduct(item));
    }
    return (
        <div className={classes.root}>
            <Grid container spacing={2}>
                <Grid item xs={12} md={12}>
                    {
                        
                        cartItems.length > 0 ? 
                        <div>
                        {cartItems.map((item, index) => {
                            return (
                                <Grid item xs={12} key={index}>
                                    <Typography component="span" variant="body1">
                                        {item.name} x {item.quantity} = &#8377; {item.quantity*item.price}
                                    </Typography>
                                    {/* <Button size="small" color="secondary" variant="outlined" onClick={handleRemoveProduct(item)}>-</Button> */}
                                </Grid>
                            )
                        })}
                        <Grid item md={12}>
                            <Typography component="p" variant="body2">
                                Total: &#8377; {cartTotal}
                            </Typography>
                        </Grid>
                        <Link to="/checkout" className="btn btn-primary btn-block">
                            Checkout
                        </Link>
                        </div>
                        : 
                        <div>
                            Your cart is empty!
                        </div>
                    }
                </Grid>
            </Grid>
        </div>
    )
}

export default rightSidebar;
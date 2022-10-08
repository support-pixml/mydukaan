import { Button, Grid, List, ListItem, makeStyles, Typography } from '@material-ui/core';
import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Link } from 'react-router-dom';
import { removeProduct, removeProductOption } from '../../actions/cart';
import {BiTrashAlt} from 'react-icons/bi';

const useStyles = makeStyles((theme) => ({
    root: {
        flexGrow: 1,
        maxWidth: 800,
        position: '-webkit-sticky',
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

const rightSidebar = ({auth}) => {

    const classes = useStyles();
    const cartItems = useSelector((state) => state.cart.cartItems);
    const cartTotal = useSelector((state) => state.cart.total);
    const dispatch = useDispatch();

    const handleRemoveProduct = (item) => {
        if(!item.option_name)
            dispatch(removeProduct(item));
        else
            dispatch(removeProductOption(item));
    }

    // if(auth.role === 'seller')
    // {
    //     return false;
    // }
    
    return (
        <div className={classes.root}>
            <Grid container spacing={2}>
                <Grid item xs={12} md={12}>
                    {                        
                        cartItems.length > 0 ? 
                        <List className={classes.root}>
                        {cartItems.map((item, index) => {
                            return (
                                <ListItem alignItems="flex-start" key={index}>   
                                    {!item.option_name ? 
                                    <Typography component="span" variant="body1">
                                        {item.name} x {item.quantity} = &#8377;{item.quantity*item.price}
                                    </Typography>
                                    :
                                    <Typography component="span" variant="body1">
                                        {item.product_name} - ({item.option_name}) x {item.quantity} = &#8377;{item.quantity*item.price}
                                    </Typography>
                                    }                                     
                                    <Button size="small" color="secondary" className="float-right" variant="text" onClick={() => handleRemoveProduct(item)}>
                                        <BiTrashAlt />
                                    </Button>
                                </ListItem>
                            )
                        })}
                        <ListItem alignItems="flex-start">
                            <Typography component="p" variant="body1">
                                Total: &#8377; {cartTotal}
                            </Typography>
                        </ListItem>
                        <Link to="/checkout" className="btn btn-primary btn-block">
                            Checkout
                        </Link>
                        </List>
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
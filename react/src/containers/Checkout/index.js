import { Button, Container, CssBaseline, FormControl, Grid, makeStyles, Typography } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { Link } from 'react-router-dom';
import { checkOut } from '../../actions/cart';
import Input from '../../components/UI/Input';

const initialState = {
    customer_name: '', company_name: '', customer_email: '', customer_phone: '', address: '', note: ''
};

const useStyles = makeStyles((theme) => ({
    paper: {
        marginTop: theme.spacing(15),
        display: 'flex',
        flexDirection: 'column',
        alignItems: 'center',
    },
    form: {
        width: '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
    },
    submit: {
        margin: theme.spacing(3, 0, 2),
    },
    formControl: {
        width: '100%', 
    },
}));

const CheckOut = () => {
    const classes = useStyles();

    const carts = useSelector((state) => state.cart);

    const [checkoutData, setCheckoutData] = useState(initialState);
    const dispatch = useDispatch();
    
    const submitOrder = (e) => {
        e.preventDefault();
        console.log('submit', carts);
        // setCheckoutData({...checkoutData, cart: carts.cartItems});
        dispatch(checkOut({...checkoutData, cart: carts.cartItems}));
        setCheckoutData(initialState);
    }

    const handleChange = (e) => {
        setCheckoutData({...checkoutData, [e.target.name]: e.target.value});
    }

    return (
        <Container component="main" maxWidth="lg">
            <CssBaseline />
            <div className={classes.paper}>
                <Grid container spacing={4}>
                    <Grid item xs={12} md={6}>
                        <Typography component="h1" variant="h5">
                        Checkout
                        </Typography>
                        <form className={classes.form} noValidate onSubmit={submitOrder}>
                            <Grid container spacing={2}>
                                <Grid item xs={12} md={6}>
                                    <Input
                                        name="customer_name"
                                        variant="outlined"
                                        required
                                        fullWidth
                                        id="customerName"
                                        label="Customer Name"
                                        placeholder="Customer Name"
                                        autoFocus
                                        value={checkoutData.customer_name}
                                        handleChange={handleChange}
                                    />
                                </Grid>       
                                <Grid item xs={12} md={6}>
                                    <Input
                                        name="company_name"
                                        variant="outlined"
                                        required
                                        fullWidth
                                        id="customerCompany"
                                        label="Customer Company"
                                        placeholder="Customer Company"                                        
                                        value={checkoutData.company_name}
                                        handleChange={handleChange}
                                    />
                                </Grid>
                                <Grid item xs={12}>
                                    <Input
                                        variant="outlined"
                                        required
                                        fullWidth
                                        rows={3}
                                        name="address"
                                        label="Address"
                                        placeholder="Address"
                                        id="address"
                                        multiline
                                        value={checkoutData.address}
                                        handleChange={handleChange}
                                    />
                                </Grid>       
                                <Grid item xs={12} md={6}>
                                    <Input
                                        name="customer_email"
                                        variant="outlined"
                                        required
                                        fullWidth
                                        id="customerEmail"
                                        label="Customer Email"
                                        placeholder="Customer Email"
                                        value={checkoutData.customer_email}
                                        handleChange={handleChange}
                                    />
                                </Grid>       
                                <Grid item xs={12} md={6}>
                                    <Input
                                        name="customer_phone"
                                        variant="outlined"
                                        required
                                        fullWidth
                                        id="customerPhone"
                                        label="Customer Phone"
                                        placeholder="Customer Phone"
                                        value={checkoutData.customer_phone}
                                        handleChange={handleChange}
                                    />
                                </Grid>       
                                <Grid item xs={12}>
                                    <Input
                                        variant="outlined"
                                        required
                                        fullWidth
                                        rows={5}
                                        name="note"
                                        label="Note"
                                        placeholder="Note"
                                        id="note"
                                        multiline
                                        value={checkoutData.note}
                                        handleChange={handleChange}
                                    />
                                </Grid>
                                <Button
                                    type="submit"
                                    fullWidth
                                    variant="contained"
                                    color="primary"
                                    className={classes.submit}
                                >
                                    Submit
                                </Button>
                            </Grid>
                        </form>
                    </Grid>       
                    <Grid item md={6} xs={12}>
                        <Typography component="h4" variant="h5">
                        Cart Items
                        </Typography>
                        {carts.cartItems.map((item, index) => {
                            if(!item.option_name)
                            {
                                return (
                                    <div key={index}>
                                        <Typography component="span" variant="body1">
                                        {item.name} 
                                        </Typography>
                                        <Typography component="span" variant="body1">
                                        {` x ${item.quantity}`}
                                        </Typography>
                                        <Typography component="span" variant="body1">
                                        {` = ₹${item.price*item.quantity}`}
                                        </Typography>
                                    </div>
                                )
                            }
                            else
                            {
                                return (
                                    <div key={index}>
                                        <Typography component="span" variant="body1">
                                        {`${item.product_name} - ${item.option_name}`} 
                                        </Typography>
                                        <Typography component="span" variant="body1">
                                        {` x ${item.quantity}`}
                                        </Typography>
                                        <Typography component="span" variant="body1">
                                        {` = ₹${item.price*item.quantity}`}
                                        </Typography>
                                    </div>
                                )
                            }
                        })}
                        <Grid item md={12} xs={6}>
                            Total: &#8377; {carts.total}
                        </Grid>
                        <Link to="/" className="btn btn-primary">
                            Back to Cart
                        </Link>
                    </Grid>         
                </Grid>
            </div>
        </Container>
    )
}

export default CheckOut;
import { Accordion, AccordionDetails, AccordionSummary, Button, Container, Divider, FormControl, FormControlLabel, Grid, makeStyles, Switch, TextField, Typography } from '@material-ui/core';
import React, { useEffect, useRef, useState } from 'react';
import { ValidatorForm } from 'react-material-ui-form-validator';
import { useDispatch, useSelector } from 'react-redux';
import { Link, Redirect } from 'react-router-dom';
import { checkOut } from '../../actions/cart';
import Input from '../../components/UI/Input';
import {BiArrowBack} from 'react-icons/bi';
import {MdExpandMore} from 'react-icons/md';
import { getUsers } from '../../actions/users';
import { isUserLoggedIn } from '../../actions/auth';
import { Autocomplete } from '@material-ui/lab';
import CartButton from '../../components/UI/CartButton';

const initialState = {
    customer_name: '', company_name: '', customer_email: '', customer_phone: '', address: '', note: '', reference: '', is_reserve: false
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
    scrollbar: {
        overflow: 'auto',
    }
}));

const CheckOut = () => {
    const classes = useStyles();
    const form = useRef(null);

    const carts = useSelector((state) => state.cart);    
    const auth = useSelector((state) => state.auth.authData);
    const users = useSelector((state) => state.user.users);

    const [checkoutData, setCheckoutData] = useState(initialState);
    const [disabled, setDisabled] = useState(false);
    const [disableSubmit, setDisableSubmit] = useState(false);
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(getUsers());
        if(!auth?.user)
            dispatch(isUserLoggedIn());
    }, []);
    
    const submitOrder = (e) => {
        e.preventDefault();
        setDisableSubmit(true);
        dispatch(checkOut({...checkoutData, cart: carts.cartItems, user_id: auth.user.id, user_role: auth.user.role}));
        setCheckoutData(initialState);
    }

    const response = useSelector(state => state.cart);

    const handleChange = (e) => {
        if(e.target.name == 'is_reserve')
            setCheckoutData({...checkoutData, [e.target.name]: e.target.checked});
        else
            setCheckoutData({...checkoutData, [e.target.name]: e.target.value});
    }

    if(carts?.cartItems.length === 0)
    {
        return <Redirect to={`/`} />
    }

    return (
        <Container component="main" maxWidth="lg">
            <div className={classes.paper}>
                <Grid container md={6} spacing={4}>
                    <Grid item md={12} xs={12}>
                        <Accordion>
                            <AccordionSummary
                                expandIcon={<MdExpandMore />}
                                aria-controls="panel1a-content"
                                id="panel1a-header"
                                >
                                <Typography className={classes.heading}>Cart</Typography>
                                </AccordionSummary>
                                <AccordionDetails>
                                    <Container>
                                    <Grid container item md={12} xs={12}>
                                        <Grid item md={5} xs={4}>
                                            <Typography component="p" variant="h6" color="textSecondary">
                                            Product
                                            </Typography>
                                        </Grid>
                                        <Grid item md={4} xs={4}>
                                            <Typography component="p" variant="h6" color="textSecondary">
                                            Qty
                                            </Typography>
                                        </Grid>
                                        <Grid item md={2} xs={4}>
                                            <Typography component="p" variant="h6" color="textSecondary" className="float-right">
                                            Amount
                                            </Typography>
                                        </Grid>
                                    </Grid>
                            <Divider style={{margin:'5px 0'}} />
                            <Grid className={classes.scrollbar}>
                            {carts.cartItems.map((item, index) => {
                                return (
                                    <>
                                    <Grid container item md={12} xs={12} key={index}>
                                        <Grid item md={5} xs={4}>
                                            <Typography component="p" variant="body1">
                                            {item.name}
                                            </Typography>
                                        </Grid>
                                        <Grid item md={4} xs={4}>
                                            <CartButton product={item} cartItems={carts.cartItems} auth={auth} setInputDisabled={setDisabled} />     
                                        </Grid>
                                        <Grid item md={2} xs={4}>
                                            <Typography component="p" variant="body1" className="float-right">
                                            &#8377;{item.quantity*item.price}
                                            </Typography>
                                        </Grid>
                                    </Grid>
                                    <Divider style={{margin:'5px 0'}} />
                                    </>
                                )
                            })}
                            </Grid>
                            <Grid item md={12} xs={12} style={{margin:'15px 0'}}>
                            <Typography component="p" variant="body1">
                                Total: &#8377; {carts.total}
                            </Typography>
                            </Grid>
                            <Link to="/" className="btn btn-block text-danger" style={{margin:'20px 0'}}>
                            <BiArrowBack /> Back to Cart
                            </Link>
                                    </Container>
                            
                            </AccordionDetails>
                        </Accordion>    
                    </Grid>     
                    <Grid item xs={12} md={12}>
                        <Typography component="h1" variant="h5">
                        Checkout
                        </Typography>
                        <ValidatorForm className={classes.form} ref={form} noValidate onSubmit={submitOrder}>
                            <Grid container spacing={2}>                                      
                                <Grid item xs={12} md={6}>
                                    <Input
                                        name="company_name"
                                        variant="outlined"
                                        required
                                        fullWidth
                                        autoFocus
                                        id="customerCompany"
                                        label="Company Name"
                                        placeholder="Company Name"                                        
                                        value={checkoutData.company_name}
                                        handleChange={handleChange}
                                        validators={['required']}
                                        errorMessages={['this field is required']}
                                    />
                                </Grid>    
                                <Grid item xs={12} md={6}>
                                    <Input
                                        name="customer_name"
                                        variant="outlined"
                                        fullWidth
                                        id="customerName"
                                        label="Customer Name"
                                        placeholder="Customer Name"                                       
                                        value={checkoutData.customer_name}
                                        handleChange={handleChange}                                        
                                    />
                                </Grid> 
                                <Grid item xs={12} md={6}>
                                    <Input
                                        name="customer_phone"
                                        variant="outlined"
                                        fullWidth
                                        id="customerPhone"
                                        label="Customer Phone"
                                        placeholder="Customer Phone"
                                        value={checkoutData.customer_phone}
                                        handleChange={handleChange}
                                    />
                                </Grid>
                                <Grid item xs={12} md={6}>
                                    <Input
                                        name="customer_email"
                                        variant="outlined"
                                        fullWidth
                                        id="customerEmail"
                                        label="Customer Email"
                                        placeholder="Customer Email"
                                        value={checkoutData.customer_email}
                                        handleChange={handleChange}
                                        validators={['isEmail']}
                                        errorMessages={['enter proper email.']}
                                    />
                                </Grid>   
                                <Grid item xs={12} md={6}>
                                    <FormControl variant="outlined" className={classes.formControl}>
                                        <Autocomplete
                                            id="select-product"
                                            options={users}
                                            getOptionLabel={(user) => user.name}
                                            onChange={(event, newValue) => {
                                                setCheckoutData({...checkoutData, reference: newValue.id});
                                            }}
                                            renderInput={(params) => <TextField {...params} className={classes.textField} label="Select Reference" variant="outlined" />}
                                        />
                                    </FormControl>
                                </Grid>   
                                <Grid item xs={12}>
                                    <Input
                                        variant="outlined"
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
                                {(auth?.user.role == 1 || auth?.user.role == 2) &&
                                <Grid item xs={12}>
                                    <FormControlLabel
                                        control={
                                        <Switch
                                            checked={checkoutData.is_reserve}
                                            onChange={handleChange}
                                            name="is_reserve"
                                            color="primary"
                                        />
                                        }
                                        label="Reserve This Order"
                                    />
                                </Grid>
                                }
                                <Button
                                    type="submit"
                                    fullWidth
                                    variant="contained"
                                    color="primary"
                                    className={classes.submit}
                                    disabled={disableSubmit}
                                >
                                    {disableSubmit ? 'Please Wait...' : 'Submit'}
                                </Button>
                            </Grid>
                        </ValidatorForm>
                    </Grid>       
                        
                </Grid>
            </div>
        </Container>
    )
}

export default CheckOut;
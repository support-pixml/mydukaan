import { Backdrop, Button, Fade, Grid, makeStyles, Modal, Typography } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import { ValidatorForm } from 'react-material-ui-form-validator';
import { useDispatch } from 'react-redux';
import { addProductStock } from '../../actions/products';
import Input from '../../components/UI/Input';

const initialState = {product_name: '', documentId: null, stock: null};

const useStyles = makeStyles((theme) => ({
    modal: {
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
    },
    paper: {
        backgroundColor: theme.palette.background.paper,
        border: '2px solid #000',
        boxShadow: theme.shadows[5],
        padding: theme.spacing(2, 4, 3),
        width: '500px'
    },
    form: {
        width: '100%', // Fix IE 11 issue.
        marginTop: theme.spacing(3),
    },
    submit: {
        margin: theme.spacing(3, 0, 2),
    },
}));

const AddProductStock = ({handleClose, open, product}) => {
    const classes = useStyles();
    const [stockData, setStockData] = useState(initialState);
    const dispatch = useDispatch();
    var formData = new FormData();

    const submitStock = async (e) => {
        e.preventDefault();
        formData.append('long_id', product.long_id);
        formData.append('documentId', stockData.documentId);
        formData.append('stock', stockData.stock);
        dispatch(addProductStock(formData));
        setStockData(initialState);
        handleClose();
    }

    useEffect(() => {
        if(product)
        {
            setStockData({ ...stockData, product_name: product.name});
        }
        else
        {
            setStockData(initialState);
        }
    }, [product]);

    return (
        <Modal
            aria-labelledby="transition-modal-title"
            aria-describedby="transition-modal-description"
            className={classes.modal}
            open={open}
            onClose={handleClose}
            closeAfterTransition
            BackdropComponent={Backdrop}
            BackdropProps={{
                timeout: 500,
            }}
        >
            <Fade in={open}>
                <div className={classes.paper}>
                    {product && <Typography component="h1" variant="h5">
                        {stockData.product_name}
                    </Typography>}
                    <ValidatorForm className={classes.form} noValidate onSubmit={submitStock}>
                        <Grid container spacing={2}>
                            <Grid item xs={12}>
                                <Input
                                    name="documentId"
                                    variant="outlined"
                                    required
                                    fullWidth
                                    id="documentId"
                                    label="Document Id"
                                    placeholder="Document Id"
                                    autoFocus
                                    value={stockData.documentId}
                                    handleChange={(e) => setStockData({...stockData, documentId: e.target.value})}
                                />
                            </Grid>   
                            <Grid item xs={12}>
                                <Input
                                    name="stock"
                                    variant="outlined"
                                    required
                                    fullWidth
                                    id="stock"
                                    label="Stock"
                                    placeholder="Stock"
                                    value={stockData.stock}
                                    handleChange={(e) => setStockData({...stockData, stock: e.target.value})}
                                />
                            </Grid>   
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
                    </ValidatorForm>
                </div>
            </Fade>
        </Modal>
    );
}

export default AddProductStock;
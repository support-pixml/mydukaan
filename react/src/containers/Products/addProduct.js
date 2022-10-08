import { Accordion, AccordionDetails, AccordionSummary, Button, Checkbox, Container, FormControl, FormControlLabel, Grid, makeStyles, Select, Typography } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import Input from '../../components/UI/Input';
import {useDispatch, useSelector} from 'react-redux';
import { getCategories } from '../../actions/categories';
import { addProduct, updateProduct } from '../../actions/products';
import { ValidatorForm } from 'react-material-ui-form-validator';
import { MdExpandMore } from 'react-icons/md';

const initialState = {
    name: '', category_id: '', price: '', description: '', min_stock_qty: '', is_favorite: false
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
    selectEmpty: {
        marginTop: theme.spacing(2),
    }

}));

const AddProduct = ({expanded, product, setExpanded, message, onMessageHandler}) => {
    const classes = useStyles();
    const [productData, setProductData] = useState(initialState);
    const [selectedFile, setSelectedFile] = useState(''); 
    const [isFavoriteState, setIsFavoriteState] = useState(false);
    const dispatch = useDispatch();

    useEffect(() => {
        if(expanded === 'panel1') dispatch(getCategories());
    }, [expanded]);

    useEffect(() => {
        reset();
        if(product)
        {
            setProductData({ ...product});
            setSelectedFile('');
            if(product.is_favorite === '1')
                setIsFavoriteState(true);
            else
                setIsFavoriteState(false);
        }  
        else
        {
            setProductData(initialState);
            setSelectedFile('');
            setIsFavoriteState(false);
        }      
    }, [product])

    const categories = useSelector((state) => state.category.categories);

    const submitProduct = (e) => {
        e.preventDefault();        
        if(product)
        {
            dispatch(updateProduct(product.long_id, {...productData, image: selectedFile }));
        }
        else 
        {
            dispatch(addProduct({...productData, image: selectedFile}));
        }   
        onMessageHandler();       
    }

    const reset = () => {
        document.getElementById('contained-button-file').value = '';
    }

    useEffect(() => {
        if(message)
        {
            setProductData(initialState);
            setSelectedFile('');
            setExpanded('');            
        }
    }, [message]);

    let ImageBase64 = '';
    const handleChange = e => {
        if(e.target.name !== '')
        {
            setProductData({...productData, [e.target.name]: e.target.value});
        }
        if(e.target.name === 'is_favorite')
        {
            setProductData({...productData, [e.target.name]: !isFavoriteState});
            setIsFavoriteState(!isFavoriteState);
        }
        if(e.target.name === 'product_image')
        {
            getBase64(e.target.files[0], (result) => {
                ImageBase64 = result;
                setSelectedFile(ImageBase64);
           });
        }
    };   

    const getBase64 = (file, cb) => {
        let reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function () {
            cb(reader.result)
        };
        reader.onerror = function (error) {
            console.log('Error: ', error);
        };
    }
    
    return (
            <Accordion expanded={expanded === 'panel1'}>
                <AccordionSummary
                    expandIcon={<MdExpandMore />}                    
                    aria-controls="panel1a-content"
                    id="panel1a-header"
                    >
                    <Typography className={classes.heading}>{product ? 'Edit' : 'Add'} Product</Typography>
                </AccordionSummary>
                <AccordionDetails>
                    <Container>
                        <Grid container item md={12} xs={12}>
                            <Grid item xs={12} md={6}>
                                <ValidatorForm className={classes.form} noValidate onSubmit={submitProduct} onChange={handleChange}>
                                    <Grid container spacing={2}>
                                    <Grid item xs={12}>
                                        <Input
                                            name="name"
                                            variant="outlined"
                                            required
                                            fullWidth
                                            id="productName"
                                            label="Product Name"
                                            placeholder="Product Name"
                                            autoFocus
                                            value={productData.name}
                                            validators={['required']}
                                            errorMessages={['this field is required']}
                                        />
                                    </Grid>                    
                                    <Grid item xs={12}>
                                        <FormControl variant="outlined" className={classes.formControl}>
                                            <Select
                                                native
                                                value={productData.category_id}
                                                inputProps={{
                                                    name: 'category_id',
                                                    id: 'outlined-category-native-simple',
                                                }}
                                            >
                                            <option aria-label="None" value="">Select Category</option>
                                            {categories.map((category) => (
                                                <option key={category.id} value={category.id}>{category.name}</option>
                                            ))}
                                            </Select>
                                        </FormControl>
                                    </Grid>
                                    <Grid item xs={12}>
                                        <Input
                                            variant="outlined"
                                            fullWidth
                                            name="price"
                                            label="Price"
                                            placeholder="Price"
                                            id="price"
                                            value={productData.price}
                                        />
                                    </Grid>
                                    <Grid item xs={12}>
                                        <Input
                                            variant="outlined"
                                            fullWidth
                                            name="min_stock_qty"
                                            label="Min. Stock Quantity"
                                            placeholder="Min. Stock Quantity"
                                            id="min_stock_qty"
                                            value={productData.min_stock_qty}
                                        />
                                    </Grid>
                                    <Grid item xs={12}>
                                        <Input
                                            variant="outlined"
                                            fullWidth
                                            rows={5}
                                            name="description"
                                            label="Description"
                                            placeholder="Description"
                                            id="description"
                                            multiline
                                            value={productData.description}
                                        />
                                    </Grid>
                                    <Grid item xs={12}>
                                        <FormControlLabel
                                            control={
                                            <Checkbox
                                                checked={isFavoriteState}
                                                name="is_favorite"
                                                color="primary"
                                            />
                                            }
                                            label="Is Favorite?"
                                        />
                                    </Grid>
                                    <Grid item xs={12}>
                                        {/* <FileBase type="file" multiple={false} onDone={({ base64 }) => setSelectedFile(base64)} /> */}
                                        <label htmlFor="contained-button-file">
                                            <input accept="image/jpeg, image/jpg" name="product_image" id="contained-button-file" type="file" />
                                        </label>
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
                                </ValidatorForm>                                  
                            </Grid>
                        </Grid>
                    </Container>                
            </AccordionDetails>
        </Accordion>   
    )
}

export default AddProduct;
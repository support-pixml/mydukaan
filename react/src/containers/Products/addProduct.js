import { Button, Container, CssBaseline, FormControl, Grid, InputLabel, makeStyles, Select, TextareaAutosize, Typography } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import { Link, Redirect } from 'react-router-dom';
import Input from '../../components/UI/Input';
import {useDispatch, useSelector} from 'react-redux';
import { getCategories } from '../../actions/categories';
import { addProduct } from '../../actions/products';

const initialState = {
    name: '', image: null, category_id: '', price: '', stock: '', description: ''
};

const useStyles = makeStyles((theme) => ({
    paper: {
        marginTop: theme.spacing(8),
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
    },
}));

const AddProduct = () => {
    const classes = useStyles();
    const [productData, setProductData] = useState(initialState);
    const dispatch = useDispatch();
    var formData = new FormData();

    // const user = localStorage.getItem('profile');

    // if(user)
    // {
    //     return <Redirect to={`/`} />
    // }

    useEffect(() => {
        dispatch(getCategories());
    }, [dispatch]);

    const categories = useSelector((state) => state.categories);

    const submitProduct = (e) => {
        e.preventDefault();
        formData.append('name', productData.name);
        formData.append('category_id', productData.category_id);
        formData.append('price', productData.price);
        formData.append('stock', productData.stock);
        formData.append('description', productData.description);
        formData.append('image', productData.image);
        dispatch(addProduct(formData));
    }

    return (
        <Container component="main" maxWidth="xs">
            <CssBaseline />
            <div className={classes.paper}>
                <Typography component="h1" variant="h5">
                Add Product
                </Typography>
                <form className={classes.form} noValidate onSubmit={submitProduct}>
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
                            handleChange={(e) => setProductData({...productData, name: e.target.value})}
                        />
                    </Grid>                    
                    <Grid item xs={12}>
                        <FormControl variant="outlined" className={classes.formControl}>
                            <InputLabel htmlFor="outlined-category-native-simple">Category</InputLabel>
                            <Select
                                native
                                value={formData.category_id}
                                onChange={(e) => setProductData({...productData, category_id: e.target.value})}
                                label="Category"
                                inputProps={{
                                    name: 'category',
                                    id: 'outlined-category-native-simple',
                                }}
                            >
                            <option aria-label="None" value="" />
                            {categories.map((category) => (
                                <option key={category.id} value={category.id}>{category.name}</option>
                            ))}
                            </Select>
                        </FormControl>
                    </Grid>
                    <Grid item xs={12}>
                        <Input
                            variant="outlined"
                            required
                            fullWidth
                            name="price"
                            label="Price"
                            placeholder="Price"
                            id="price"
                            handleChange={(e) => setProductData({...productData, price: e.target.value})}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <Input
                            variant="outlined"
                            required
                            fullWidth
                            name="stock"
                            label="Stock"
                            placeholder="Stock"
                            id="stock"
                            handleChange={(e) => setProductData({...productData, stock: e.target.value})}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <Input
                            variant="outlined"
                            required
                            fullWidth
                            rows={5}
                            name="description"
                            label="Description"
                            placeholder="Description"
                            id="description"
                            multiline
                            handleChange={(e) => setProductData({...productData, description: e.target.value})}
                        />
                    </Grid>
                    <Grid item xs={12}>
                        <input type="file" onChange={(e) => setProductData({...productData, image: e.target.files[0]})} />
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
                </form>
            </div>
        </Container>
    )
}

export default AddProduct;
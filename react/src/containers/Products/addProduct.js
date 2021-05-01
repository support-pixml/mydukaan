import { Avatar, Button, Checkbox, Container, CssBaseline, FormControl, FormControlLabel, Grid, InputLabel, makeStyles, Paper, Select, Table, TableBody, TableCell, TableContainer, TableHead, TableRow, TextareaAutosize, Typography } from '@material-ui/core';
import React, { useEffect, useState } from 'react';
import Input from '../../components/UI/Input';
import {useDispatch, useSelector} from 'react-redux';
import { getCategories } from '../../actions/categories';
import { addProduct, getProducts } from '../../actions/products';
import ProductOptions from './addProductOptions';

const initialState = {
    name: '', image: null, category_id: '', price: '', stock: '', description: ''
};

const initialOptionsState = [
    {
        index: Math.random(),
        option_name: '',
        option_price: '',
        option_stock: ''
    }
]

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
    },
    table: {
        minWidth: 650,
    },
}));

const AddProduct = () => {
    const classes = useStyles();
    const [productData, setProductData] = useState(initialState);
    const [productOptions, setProductOptions] = useState(initialOptionsState);
    const [isOption, setIsOption] = useState(false);
    const dispatch = useDispatch();
    var formData = new FormData();

    useEffect(() => {
        dispatch(getCategories());
    }, [dispatch]);

    useEffect(() => {
        dispatch(getProducts());
    }, [dispatch]);

    const categories = useSelector((state) => state.category.categories);
    const products = useSelector((state) => state.product.products);

    const submitProduct = (e) => {
        e.preventDefault();
        formData.append('name', productData.name);
        formData.append('category_id', productData.category_id);
        formData.append('price', productData.price);
        formData.append('stock', productData.stock);
        formData.append('description', productData.description);
        formData.append('image', productData.image);
        console.log(formData);
        dispatch(addProduct(formData));
        setProductData(initialState);
    }

    const renderProductsTable = () => {
        return (
            <TableContainer component={Paper}>
                <Table className={classes.table} aria-label="simple table">
                    <TableHead>
                    <TableRow>
                        <TableCell>Product Name</TableCell>
                        <TableCell>Price</TableCell>
                        <TableCell>Stock</TableCell>
                        <TableCell>Image</TableCell>
                        <TableCell>Action</TableCell>
                    </TableRow>
                    </TableHead>
                    <TableBody>
                    {products.map((product) => (
                        <TableRow key={product.long_id}>
                        <TableCell component="th" scope="row">
                            {product.name}
                        </TableCell>
                        <TableCell>&#8377;{product.price}</TableCell>
                        <TableCell>{product.stock}</TableCell>
                        <TableCell>
                            <Avatar alt={product.name} variant="rounded" src={`/uploads/products/${product.image}`} />
                        </TableCell>
                        <TableCell></TableCell>
                        </TableRow>
                    ))}
                    </TableBody>
                </Table>
            </TableContainer>
        )
    } 

    const addNewRow = e => {
        setProductOptions([
            ...productOptions,
            {
            index: Math.random(),
            option_name: "",
            option_price: "",
            option_stock: "",
            }
        ])
    };

    const clickOnDelete = (record) => {
        setProductOptions(productOptions.filter(r => r !== record));
    }

    return (
        <Container component="main" maxWidth="lg">
            <CssBaseline />
            <div className={classes.paper}>
            <Grid container spacing={2}>
                <Grid item xs={12} md={8}>
                    {renderProductsTable()}
                </Grid>
                <Grid item xs={12} md={4}>
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
                        <Grid item xs={12}>
                            <FormControlLabel
                                control={
                                <Checkbox
                                    checked={isOption}
                                    onChange={() => setIsOption(!isOption)}
                                    name="isOption"
                                    color="primary"
                                />
                                }
                                label="Has Options?"
                            />
                        </Grid>
                        {isOption ? (
                        <Grid item xs={12}>
                            <ProductOptions
                                add={addNewRow}
                                deleteRow={clickOnDelete.bind(this)}
                                bookDetails={productOptions}
                            />
                        </Grid>
                        ): null}
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
                </Grid>
            </Grid>

            </div>
        </Container>
    )
}

export default AddProduct;
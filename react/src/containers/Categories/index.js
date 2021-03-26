import { Typography } from '@material-ui/core';
import React, { useEffect } from 'react';
import { Col, Container, Image, Row } from 'react-bootstrap';
import { useDispatch, useSelector } from 'react-redux';
import { getCategories } from '../../actions/categories';

const Categories = () => {
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(getCategories());
    }, [dispatch]);

    const categories = useSelector((state) => state.categories);

    return (
        <Container className="mt-3">
            <Row>
                <Col md={{span:12}}>
                    <h3>All Categories</h3>
                </Col>
            </Row>
            <Row>
                {categories.map((category) => (
                    <Col className="pt-3" md={{span:3}} xs={{span:4}} key={category.id}>
                        <Image src={`/uploads/categories/${category.image}`} alt={category.name} width="100%" rounded />
                        <Typography variant="h5" component="h3">
                            {category.name}
                        </Typography>
                    </Col>
                ))}
            </Row>
        </Container>
    )
}

export default Categories;
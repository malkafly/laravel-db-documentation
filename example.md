# Database Documentation: `classicmodels`

This document was automatically generated to provide an overview of the MySQL database schema used in this Laravel project.
It is optimized to support AI-assisted development by summarizing tables, columns, associated Eloquent models,
fillable attributes, casts, hidden fields, and model relationships. Each section includes an example JSON payload
(retrieved or mocked) to give context for the structure of the data.

## Table: `customers`

| Column | Type | Nullable | Key | Default | Extra |
|--------|------|----------|-----|---------|-------|
| `customerNumber` | `int` | `NO` | `PRI` | `NULL` | `` |
| `customerName` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `contactLastName` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `contactFirstName` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `phone` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `addressLine1` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `addressLine2` | `varchar(50)` | `YES` | `` | `NULL` | `` |
| `city` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `state` | `varchar(50)` | `YES` | `` | `NULL` | `` |
| `postalCode` | `varchar(15)` | `YES` | `` | `NULL` | `` |
| `country` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `salesRepEmployeeNumber` | `int` | `YES` | `MUL` | `NULL` | `` |
| `creditLimit` | `decimal(10,2)` | `YES` | `` | `NULL` | `` |

### Example JSON:

```json
{
    "customerNumber": 103,
    "customerName": "Atelier graphique",
    "contactLastName": "Schmitt",
    "contactFirstName": "Carine ",
    "phone": "40.32.2555",
    "addressLine1": "54, rue Royale",
    "addressLine2": null,
    "city": "Nantes",
    "state": null,
    "postalCode": "44000",
    "country": "France",
    "salesRepEmployeeNumber": 1370,
    "creditLimit": "21000.00"
}
```

## Table: `employees`

| Column | Type | Nullable | Key | Default | Extra |
|--------|------|----------|-----|---------|-------|
| `employeeNumber` | `int` | `NO` | `PRI` | `NULL` | `` |
| `lastName` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `firstName` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `extension` | `varchar(10)` | `NO` | `` | `NULL` | `` |
| `email` | `varchar(100)` | `NO` | `` | `NULL` | `` |
| `officeCode` | `varchar(10)` | `NO` | `MUL` | `NULL` | `` |
| `reportsTo` | `int` | `YES` | `MUL` | `NULL` | `` |
| `jobTitle` | `varchar(50)` | `NO` | `` | `NULL` | `` |

### Example JSON:

```json
{
    "employeeNumber": 1002,
    "lastName": "Murphy",
    "firstName": "Diane",
    "extension": "x5800",
    "email": "dmurphy@classicmodelcars.com",
    "officeCode": "1",
    "reportsTo": null,
    "jobTitle": "President"
}
```

## Table: `offices`

| Column | Type | Nullable | Key | Default | Extra |
|--------|------|----------|-----|---------|-------|
| `officeCode` | `varchar(10)` | `NO` | `PRI` | `NULL` | `` |
| `city` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `phone` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `addressLine1` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `addressLine2` | `varchar(50)` | `YES` | `` | `NULL` | `` |
| `state` | `varchar(50)` | `YES` | `` | `NULL` | `` |
| `country` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `postalCode` | `varchar(15)` | `NO` | `` | `NULL` | `` |
| `territory` | `varchar(10)` | `NO` | `` | `NULL` | `` |

### Example JSON:

```json
{
    "officeCode": "1",
    "city": "San Francisco",
    "phone": "+1 650 219 4782",
    "addressLine1": "100 Market Street",
    "addressLine2": "Suite 300",
    "state": "CA",
    "country": "USA",
    "postalCode": "94080",
    "territory": "NA"
}
```

## Table: `orderdetails`

| Column | Type | Nullable | Key | Default | Extra |
|--------|------|----------|-----|---------|-------|
| `orderNumber` | `int` | `NO` | `PRI` | `NULL` | `` |
| `productCode` | `varchar(15)` | `NO` | `PRI` | `NULL` | `` |
| `quantityOrdered` | `int` | `NO` | `` | `NULL` | `` |
| `priceEach` | `decimal(10,2)` | `NO` | `` | `NULL` | `` |
| `orderLineNumber` | `smallint` | `NO` | `` | `NULL` | `` |

### Example JSON:

```json
{
    "orderNumber": 10100,
    "productCode": "S18_1749",
    "quantityOrdered": 30,
    "priceEach": "136.00",
    "orderLineNumber": 3
}
```

## Table: `orders`

| Column | Type | Nullable | Key | Default | Extra |
|--------|------|----------|-----|---------|-------|
| `orderNumber` | `int` | `NO` | `PRI` | `NULL` | `` |
| `orderDate` | `date` | `NO` | `` | `NULL` | `` |
| `requiredDate` | `date` | `NO` | `` | `NULL` | `` |
| `shippedDate` | `date` | `YES` | `` | `NULL` | `` |
| `status` | `varchar(15)` | `NO` | `` | `NULL` | `` |
| `comments` | `text` | `YES` | `` | `NULL` | `` |
| `customerNumber` | `int` | `NO` | `MUL` | `NULL` | `` |

### Example JSON:

```json
{
    "orderNumber": 10100,
    "orderDate": "2003-01-06",
    "requiredDate": "2003-01-13",
    "shippedDate": "2003-01-10",
    "status": "Shipped",
    "comments": null,
    "customerNumber": 363
}
```

## Table: `payments`

| Column | Type | Nullable | Key | Default | Extra |
|--------|------|----------|-----|---------|-------|
| `customerNumber` | `int` | `NO` | `PRI` | `NULL` | `` |
| `checkNumber` | `varchar(50)` | `NO` | `PRI` | `NULL` | `` |
| `paymentDate` | `date` | `NO` | `` | `NULL` | `` |
| `amount` | `decimal(10,2)` | `NO` | `` | `NULL` | `` |

### Example JSON:

```json
{
    "customerNumber": 103,
    "checkNumber": "HQ336336",
    "paymentDate": "2004-10-19",
    "amount": "6066.78"
}
```

## Table: `productlines`

| Column | Type | Nullable | Key | Default | Extra |
|--------|------|----------|-----|---------|-------|
| `productLine` | `varchar(50)` | `NO` | `PRI` | `NULL` | `` |
| `textDescription` | `varchar(4000)` | `YES` | `` | `NULL` | `` |
| `htmlDescription` | `mediumtext` | `YES` | `` | `NULL` | `` |
| `image` | `mediumblob` | `YES` | `` | `NULL` | `` |

### Example JSON:

```json
{
    "productLine": "Classic Cars",
    "textDescription": "Attention car enthusiasts: Make your wildest car ownership dreams come true. Whether you are looking for classic muscle cars, dream sports cars or movie-inspired miniatures, you will find great choices in this category. These replicas feature superb attention to detail and craftsmanship and offer features such as working steering system, opening forward compartment, opening rear trunk with removable spare wheel, 4-wheel independent spring suspension, and so on. The models range in size from 1:10 to 1:24 scale and include numerous limited edition and several out-of-production vehicles. All models include a certificate of authenticity from their manufacturers and come fully assembled and ready for display in the home or office.",
    "htmlDescription": null,
    "image": null
}
```

## Table: `products`

| Column | Type | Nullable | Key | Default | Extra |
|--------|------|----------|-----|---------|-------|
| `productCode` | `varchar(15)` | `NO` | `PRI` | `NULL` | `` |
| `productName` | `varchar(70)` | `NO` | `` | `NULL` | `` |
| `productLine` | `varchar(50)` | `NO` | `MUL` | `NULL` | `` |
| `productScale` | `varchar(10)` | `NO` | `` | `NULL` | `` |
| `productVendor` | `varchar(50)` | `NO` | `` | `NULL` | `` |
| `productDescription` | `text` | `NO` | `` | `NULL` | `` |
| `quantityInStock` | `smallint` | `NO` | `` | `NULL` | `` |
| `buyPrice` | `decimal(10,2)` | `NO` | `` | `NULL` | `` |
| `MSRP` | `decimal(10,2)` | `NO` | `` | `NULL` | `` |

### Example JSON:

```json
{
    "productCode": "S10_1678",
    "productName": "1969 Harley Davidson Ultimate Chopper",
    "productLine": "Motorcycles",
    "productScale": "1:10",
    "productVendor": "Min Lin Diecast",
    "productDescription": "This replica features working kickstand, front suspension, gear-shift lever, footbrake lever, drive chain, wheels and steering. All parts are particularly delicate due to their precise scale and require special care and attention.",
    "quantityInStock": 7933,
    "buyPrice": "48.81",
    "MSRP": "95.70"
}
```


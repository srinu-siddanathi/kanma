# Slug Generation Test Examples

## How the Unique Slug Generation Works

The system now automatically handles duplicate slugs by appending incremental numbers to ensure uniqueness.

### Example 1: Multiple "Fresh Eggs" Products
**Scenario**: Creating multiple products with the name "Fresh Eggs"

**Generated Slugs**:
1. First "Fresh Eggs" → `fresh-eggs`
2. Second "Fresh Eggs" → `fresh-eggs-1`
3. Third "Fresh Eggs" → `fresh-eggs-2`
4. Fourth "Fresh Eggs" → `fresh-eggs-3`

### Example 2: Similar Product Names
**Scenario**: Creating products with similar names

**Generated Slugs**:
1. "Organic Apples" → `organic-apples`
2. "Fresh Organic Apples" → `fresh-organic-apples`
3. "Organic Apples Premium" → `organic-apples-premium`
4. "Organic Apples" (duplicate) → `organic-apples-1`

### Example 3: Special Characters and Spaces
**Scenario**: Products with special characters and spaces

**Generated Slugs**:
1. "Fresh & Organic Milk" → `fresh-organic-milk`
2. "Fresh & Organic Milk" (duplicate) → `fresh-organic-milk-1`
3. "Milk (2L Bottle)" → `milk-2l-bottle`
4. "Milk (2L Bottle)" (duplicate) → `milk-2l-bottle-1`

## Technical Implementation

```php
private function generateUniqueSlug($name)
{
    $slug = Str::slug($name);
    $count = 1;
    $originalSlug = $slug;

    while (Product::where('slug', $slug)->exists()) {
        $slug = $originalSlug . '-' . $count;
        $count++;
    }

    return $slug;
}
```

## Benefits

- **No More Errors**: Eliminates "Duplicate entry" SQL errors
- **SEO Friendly**: Maintains clean, readable URLs
- **Automatic**: No manual intervention required
- **Consistent**: Works across all product creation methods
- **Scalable**: Handles unlimited duplicates

## How It Works

1. **Initial Slug**: Convert product name to slug using `Str::slug()`
2. **Check Existence**: Query database to see if slug already exists
3. **Append Number**: If exists, append `-1`, `-2`, etc.
4. **Repeat**: Continue until a unique slug is found
5. **Return**: Return the unique slug

## Database Impact

- **No Schema Changes**: Uses existing `slug` field
- **Maintains Uniqueness**: Respects the unique constraint
- **Performance**: Minimal impact with proper indexing
- **Backward Compatible**: Works with existing products

## Error Prevention

**Before Fix**:
```
SQLSTATE[23000]: Integrity constraint violation: 1062 
Duplicate entry 'fresh-eggs' for key 'products.products_slug_unique'
```

**After Fix**:
- ✅ First "Fresh Eggs" → `fresh-eggs`
- ✅ Second "Fresh Eggs" → `fresh-eggs-1`
- ✅ Third "Fresh Eggs" → `fresh-eggs-2`
- ✅ No more errors!

This fix ensures that shop owners can create products with similar names without encountering database errors, while maintaining clean and SEO-friendly URLs. 
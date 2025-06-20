# Search Functionality Test Examples

## How the Improved Search Works

The search now supports flexible matching that breaks down search queries into individual words and matches them separately.

### Example 1: "fresh eggs"
**Search Query**: "fresh eggs"

**What it finds**:
- Products with "fresh eggs" in the name
- Products with just "eggs" in the name
- Products with just "fresh" in the name
- Images with "fresh" or "eggs" in the filename

**Example Results**:
- "Fresh Organic Eggs" ✅
- "Farm Fresh Eggs" ✅
- "Eggs Benedict" ✅
- "Fresh Milk" ✅
- "Organic Eggs" ✅

### Example 2: "organic tomatoes"
**Search Query**: "organic tomatoes"

**What it finds**:
- Products with "organic tomatoes" in the name
- Products with just "tomatoes" in the name
- Products with just "organic" in the name
- Images with "organic" or "tomatoes" in the filename

**Example Results**:
- "Organic Cherry Tomatoes" ✅
- "Fresh Tomatoes" ✅
- "Organic Milk" ✅
- "Tomato Sauce" ✅

### Example 3: "red apples"
**Search Query**: "red apples"

**What it finds**:
- Products with "red apples" in the name
- Products with just "apples" in the name
- Products with just "red" in the name
- Images with "red" or "apples" in the filename

**Example Results**:
- "Red Delicious Apples" ✅
- "Green Apples" ✅
- "Red Bell Peppers" ✅
- "Apple Juice" ✅

## Search Logic Breakdown

1. **Query Processing**: The search query is split into individual words
2. **Exact Phrase Match**: First tries to find exact phrase matches
3. **Individual Word Matches**: Then searches for each word separately
4. **Minimum Length**: Only searches for words with 2+ characters
5. **Case Insensitive**: All searches are case-insensitive

## Benefits

- **More Inclusive**: Finds more relevant results
- **User-Friendly**: Users don't need to know exact product names
- **Flexible**: Works with partial searches and synonyms
- **Efficient**: Still maintains good performance with proper indexing

## Technical Implementation

```php
// Split query into words
$searchTerms = array_filter(explode(' ', strtolower($query)));

// Search for exact phrase AND individual words
$products = Product::where('shop_id', $shopId)
    ->where(function($q) use ($searchTerms, $query) {
        // Exact phrase match
        $q->where('name', 'like', '%' . $query . '%');
        
        // Individual word matches
        foreach ($searchTerms as $term) {
            if (strlen($term) >= 2) {
                $q->orWhere('name', 'like', '%' . $term . '%');
            }
        }
    })
    ->get();
```

This approach ensures that users get the most relevant results while maintaining search performance. 
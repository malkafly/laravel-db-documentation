Maintaining clear database documentation is a pain.
But what if you could generate a full Markdown reference of your Laravel app’s MySQL schema – including models, columns, relationships, and even example JSON payloads – in one command?

Whether for team use or to boost your AI tools like GitHub Copilot or ChatGPT, this command does exactly that.

---

**What it generates:**

* All tables with column types, defaults, and nullable info
* Grouped Laravel core tables at the bottom
* Associated Eloquent model (if found)
* Model details like `$fillable`, `$casts`, `$hidden`
* Model relationships (`hasOne`, `hasMany`, `belongsTo`)
* Example JSON for each table (live data or mocked)
* Output in clean, readable Markdown

---

**How to use it:**

1. Add the file to your Laravel app (suggested: `app/Console/Commands/GenerateDatabaseDocs.php`)
2. Register it in `App\Console\Kernel.php`
3. Run the command:

```bash
php artisan docs:database-docs
```

It will create a file like `db-documentation-yourdatabase.md` in your project root.

---

**Why it helps AI tools:**

Copilot, ChatGPT, and other LLMs work much better when you provide structured context. This `.md` file can be used to:

* Guide AI in code generation with domain-specific awareness
* Help onboard new devs with your real schema
* Build smarter scripts or migrations
* Avoid digging through migrations or dumping SQL

---

**No packages, no bloat:**
I intentionally didn’t make this a Composer package. Just copy and paste. No dependencies. Customize it freely.

---

**Conclusion:**
Let the docs write themselves. If you like this, fork it, tweak it, or add features like Swagger, YAML output, or automatic README integration.

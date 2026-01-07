# OpenAPI Specification (Markdown)

This document mirrors an OpenAPI 3.0 specification in Markdown form. It is intended to be complete and sufficient for integration.

---

## OpenAPI

```yaml
openapi: 3.0.3
info:
  title: Laravel Article Receiver API
  version: 1.0.0
  description: API for receiving articles, authors, categories, tags, and media.
servers:
  - url: https://{host}/api
    variables:
      host:
        default: target-site.com
        description: Target site host
security:
  - bearerAuth: []
components:
  securitySchemes:
    bearerAuth:
      type: http
      scheme: bearer
      bearerFormat: JWT
  parameters:
    IdempotencyKey:
      name: X-Idempotency-Key
      in: header
      required: false
      schema:
        type: string
      description: Idempotency key to prevent duplicate creation.
  schemas:
    ErrorResponse:
      type: object
      properties:
        message:
          type: string
        errors:
          type: object
      required:
        - message
    HealthResponse:
      type: object
      properties:
        status:
          type: string
          example: ok
        timestamp:
          type: string
          format: date-time
        version:
          type: string
          example: 0.1.0
      required:
        - status
        - timestamp
        - version
    ArticleCreate:
      type: object
      properties:
        title:
          type: string
          maxLength: 255
        lead:
          type: string
          maxLength: 500
        meta_description:
          type: string
          maxLength: 160
        body:
          type: string
        tags:
          type: array
          items:
            type: string
            maxLength: 100
        status:
          type: string
          example: draft
        author_id:
          type: integer
        category_id:
          type: integer
        featured_image_url:
          type: string
          maxLength: 500
        published_at:
          type: string
          format: date-time
        metadata:
          type: object
      required:
        - title
        - lead
        - meta_description
        - body
    ArticleUpdate:
      type: object
      properties:
        title:
          type: string
          maxLength: 255
        lead:
          type: string
          maxLength: 500
        meta_description:
          type: string
          maxLength: 160
        body:
          type: string
        tags:
          type: array
          items:
            type: string
            maxLength: 100
        status:
          type: string
        author_id:
          type: integer
        category_id:
          type: integer
        featured_image_url:
          type: string
          maxLength: 500
        published_at:
          type: string
          format: date-time
        metadata:
          type: object
    ArticleResponse:
      type: object
      properties:
        id:
          type: string
        title:
          type: string
        lead:
          type: string
        meta_description:
          type: string
        body:
          type: string
        tags:
          type: array
          items:
            type: string
        status:
          type: string
        url:
          type: string
        author:
          $ref: '#/components/schemas/AuthorResponse'
        category:
          $ref: '#/components/schemas/CategoryResponse'
        featured_image_url:
          type: string
        published_at:
          type: string
          format: date-time
        created_at:
          type: string
          format: date-time
        updated_at:
          type: string
          format: date-time
        metadata:
          type: object
        media:
          type: array
          items:
            $ref: '#/components/schemas/MediaResponse'
    AuthorCreate:
      type: object
      properties:
        name:
          type: string
          maxLength: 255
        email:
          type: string
          format: email
          maxLength: 255
        bio:
          type: string
        avatar_url:
          type: string
          maxLength: 500
        website:
          type: string
          maxLength: 500
      required:
        - name
    AuthorUpdate:
      type: object
      properties:
        name:
          type: string
          maxLength: 255
        email:
          type: string
          format: email
          maxLength: 255
        bio:
          type: string
        avatar_url:
          type: string
          maxLength: 500
        website:
          type: string
          maxLength: 500
    AuthorResponse:
      type: object
      properties:
        id:
          type: string
        name:
          type: string
        email:
          type: string
        bio:
          type: string
        avatar_url:
          type: string
        website:
          type: string
        articles_count:
          type: integer
    CategoryCreate:
      type: object
      properties:
        name:
          type: string
          maxLength: 255
        slug:
          type: string
          maxLength: 255
        description:
          type: string
        parent_id:
          type: integer
      required:
        - name
    CategoryUpdate:
      type: object
      properties:
        name:
          type: string
          maxLength: 255
        slug:
          type: string
          maxLength: 255
        description:
          type: string
        parent_id:
          type: integer
    CategoryResponse:
      type: object
      properties:
        id:
          type: string
        name:
          type: string
        slug:
          type: string
        description:
          type: string
        parent_id:
          type: integer
        articles_count:
          type: integer
        children:
          type: array
          items:
            $ref: '#/components/schemas/CategoryResponse'
    TagCreate:
      type: object
      properties:
        name:
          type: string
          maxLength: 255
        slug:
          type: string
          maxLength: 255
      required:
        - name
    TagUpdate:
      type: object
      properties:
        name:
          type: string
          maxLength: 255
        slug:
          type: string
          maxLength: 255
    TagResponse:
      type: object
      properties:
        id:
          type: string
        name:
          type: string
        slug:
          type: string
        articles_count:
          type: integer
    MediaResponse:
      type: object
      properties:
        id:
          type: string
        url:
          type: string
        filename:
          type: string
        mime_type:
          type: string
        size:
          type: integer
        alt_text:
          type: string
        created_at:
          type: string
          format: date-time
    MediaUpload:
      type: object
      properties:
        file:
          type: string
          format: binary
        alt_text:
          type: string
          maxLength: 255
        folder:
          type: string
          maxLength: 100
      required:
        - file
paths:
  /health:
    get:
      summary: Health check
      security:
        - bearerAuth: []
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/HealthResponse'
        '401':
          description: Unauthenticated
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ErrorResponse'
  /articles:
    get:
      summary: List articles
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/ArticleResponse'
    post:
      summary: Create article
      parameters:
        - $ref: '#/components/parameters/IdempotencyKey'
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/ArticleCreate'
      responses:
        '201':
          description: Created
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ArticleResponse'
        '409':
          description: Conflict
          content:
            application/json:
              schema:
                type: object
                properties:
                  message:
                    type: string
        '422':
          description: Validation error
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ErrorResponse'
  /articles/{id}:
    get:
      summary: Get article
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ArticleResponse'
        '404':
          description: Not found
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ErrorResponse'
    put:
      summary: Update article
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/ArticleUpdate'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ArticleResponse'
    patch:
      summary: Update article (partial)
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/ArticleUpdate'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ArticleResponse'
    delete:
      summary: Delete article
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      responses:
        '204':
          description: No Content
  /authors:
    get:
      summary: List authors
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/AuthorResponse'
    post:
      summary: Create author
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/AuthorCreate'
      responses:
        '201':
          description: Created
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/AuthorResponse'
        '422':
          description: Validation error
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ErrorResponse'
  /authors/{id}:
    get:
      summary: Get author
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/AuthorResponse'
    put:
      summary: Update author
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/AuthorUpdate'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/AuthorResponse'
    patch:
      summary: Update author (partial)
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/AuthorUpdate'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/AuthorResponse'
    delete:
      summary: Delete author
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      responses:
        '204':
          description: No Content
  /categories:
    get:
      summary: List categories
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/CategoryResponse'
    post:
      summary: Create category
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/CategoryCreate'
      responses:
        '201':
          description: Created
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/CategoryResponse'
        '422':
          description: Validation error
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ErrorResponse'
  /categories/{id}:
    get:
      summary: Get category
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/CategoryResponse'
    put:
      summary: Update category
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/CategoryUpdate'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/CategoryResponse'
    patch:
      summary: Update category (partial)
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/CategoryUpdate'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/CategoryResponse'
    delete:
      summary: Delete category
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      responses:
        '204':
          description: No Content
  /tags:
    get:
      summary: List tags
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                type: object
                properties:
                  data:
                    type: array
                    items:
                      $ref: '#/components/schemas/TagResponse'
    post:
      summary: Create tag
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/TagCreate'
      responses:
        '201':
          description: Created
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/TagResponse'
        '422':
          description: Validation error
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ErrorResponse'
  /tags/{id}:
    get:
      summary: Get tag
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/TagResponse'
    put:
      summary: Update tag
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/TagUpdate'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/TagResponse'
    patch:
      summary: Update tag (partial)
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      requestBody:
        required: true
        content:
          application/json:
            schema:
              $ref: '#/components/schemas/TagUpdate'
      responses:
        '200':
          description: OK
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/TagResponse'
    delete:
      summary: Delete tag
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      responses:
        '204':
          description: No Content
  /media:
    post:
      summary: Upload media
      requestBody:
        required: true
        content:
          multipart/form-data:
            schema:
              $ref: '#/components/schemas/MediaUpload'
      responses:
        '201':
          description: Created
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/MediaResponse'
        '422':
          description: Validation error
          content:
            application/json:
              schema:
                $ref: '#/components/schemas/ErrorResponse'
  /media/{id}:
    delete:
      summary: Delete media
      parameters:
        - name: id
          in: path
          required: true
          schema:
            type: string
      responses:
        '204':
          description: No Content
```

---

## Notes

- `tags` are stored via many-to-many relations (`article_tag`), not a JSON column.
- `auth:sanctum` is required on all routes by default.
- Use `X-Idempotency-Key` on create requests to prevent duplicates.

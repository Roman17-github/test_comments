<?php

namespace App\Controllers;

use App\Models\CommentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Comments extends BaseController
{
    private function commentModel(): CommentModel
    {
        return new CommentModel();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function sortParams(): array
    {
        $sort = $this->request->getGet('sort') ?? 'id';
        $direction = strtolower(
            $this->request->getGet('direction') ?? 'desc'
        );

        if (! in_array($sort, ['id', 'date'], true)) {
            $sort = 'id';
        }

        if (! in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'desc';
        }

        return [$sort, $direction];
    }

    /**
     * @return array{comments: list<array<string, mixed>>, pager: \CodeIgniter\Pager\Pager, sort: string, direction: string}
     */
    private function commentsPage(): array
    {
        [$sort, $direction] = $this->sortParams();
        $model = $this->commentModel();

        $comments = $model
            ->orderBy($sort, $direction)
            ->paginate(3);

        $pager = $model->pager->only(['sort', 'direction']);

        return [
            'comments'  => $comments,
            'pager'     => $pager,
            'sort'      => $sort,
            'direction' => $direction,
        ];
    }

    public function index()
    {
        return view('comments/index', $this->commentsPage());
    }

    public function feed(): ResponseInterface
    {
        $page = $this->commentsPage();

        return $this->response->setJSON([
            'html'  => view('comments/_items', [
                'comments' => $page['comments'],
            ]),
            'pager' => $page['pager']->links(),
        ]);
    }

    public function create(): ResponseInterface
    {
        $rules = [
            'name' => 'required|valid_email|max_length[255]',
            'text' => 'required',
            'date' => 'required|max_length[100]',
        ];

        if (! $this->validate($rules)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'errors' => $this->validator->getErrors(),
                ]);
        }

        $model = $this->commentModel();

        $model->insert([
            'name' => trim($this->request->getPost('name')),
            'text' => trim($this->request->getPost('text')),
            'date' => trim($this->request->getPost('date')),
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Комментарий добавлен',
        ]);
    }

    public function delete($id = null): ResponseInterface
    {
        if (! ctype_digit((string) $id) || (int) $id < 1) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['message' => 'Некорректный ID']);
        }

        $model = $this->commentModel();

        if (! $model->find((int) $id)) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['message' => 'Комментарий не найден']);
        }

        $model->delete((int) $id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Комментарий удалён',
        ]);
    }
}

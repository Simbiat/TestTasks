# Original task: Fetch users and populate all of their friends
import unittest


# Remove redundant parenthesis
class CustomError:
    def __init__(self, message):
        self.message = message

    def get_message(self):
        return self.message

    def __eq__(self, other):
        return self.message == other.message


# Remove redundant parenthesis
class UserStorage:
    def __init__(self):
        self.__vals = {
            621: {"name": "XxDragonSlayerxX", "friends": [123, 251, 631]},
            123: {"name": "FriendNo1", "friends": [621, 631]},
            251: {"name": "SecondBestFriend", "friends": [621]},
            631: {"name": "ThirdWh33l", "friends": [621, 123, 251]},
            # Adding a test for user with empty name
            321: {"name": "", "friends": []}
        }

    def get_user(self, user_id):
        if user_id in self.__vals:
            return self.__vals[user_id], None
        return None, CustomError("User not found!")


class LoadUsers(unittest.TestCase):
    def test_load_user(self):
        self.assertEqual(load_user(621), {
            "id": 621, "name": "XxDragonSlayerxX", "friends": [
                {"id": 123, "name": "FriendNo1", "friends": [621, 631]},
                {"id": 251, "name": "SecondBestFriend", "friends": [621]},
                {"id": 631, "name": "ThirdWh33l", "friends": [621, 123, 251]}
            ]
        })
        # Assuming initial value of 350 was a mistype and we need 123 as per expected results
        self.assertEqual(load_user(123), {
            "id": 123, "name": "FriendNo1", "friends": [
                {"id": 621, "name": "XxDragonSlayerxX", "friends": [123, 251, 631]},
                {"id": 631, "name": "ThirdWh33l", "friends": [621, 123, 251]}
            ]
        })
        # But still adding a test for a wrong value to ensure, that we get proper return
        self.assertEqual(load_user(350), {
            "id": None, "name": None, "friends": []
        })
        # And a test in case name is empty
        self.assertEqual(load_user(321), {
            "id": None, "name": None, "friends": []
        })
        self.assertEqual(load_user(251), {
            "id": 251, "name": "SecondBestFriend", "friends": [
                {"id": 621, "name": "XxDragonSlayerxX", "friends": [123, 251, 631]},
            ]
        })
        self.assertEqual(load_user(631), {
            "id": 631, "name": "ThirdWh33l", "friends": [
                {"id": 621, "name": "XxDragonSlayerxX", "friends": [123, 251, 631]},
                {"id": 123, "name": "FriendNo1", "friends": [621, 631]},
                {"id": 251, "name": "SecondBestFriend", "friends": [621]},
            ]
        })


db = UserStorage()


# Implement load_user
def load_user(user_id: int):
    # Get the user. We only care for the 1st element, though.
    user = db.get_user(user_id)[0]
    # Not sure if this is required in the test, but I prefer to check that entity with the ID actually exists and
    # return something, if it does not. And make sure we have a name, too.
    # In this case we can return object with same format, but with None.
    # If I was outputting this to a webpage template, I would then check if "id" of the object is None, and if it is
    # render an error page.
    # Alternatively we can adjust the UserStorage's get_user, but maybe in the future we will need custom errors there,
    # and it is technically out of scope of the task
    if user is None or not user['name']:
        return {"id": None, "name": None, "friends": []}
    # Create an empty list of friends for the new object
    friends = []
    # Populate list of friends if we have any
    if user['friends']:
        for index, friend in enumerate(user['friends']):
            # Get friend details
            toadd = db.get_user(friend)[0]
            # Add friend to list, if it's present
            if toadd:
                friends.append(toadd)
                # Add ID of the friend
                friends[-1]['id'] = friend
    # In return we can use the original ID, that was passed, get name from the "user" dict and use new friends dict
    return {"id": user_id, "name": user['name'], "friends": friends}


if __name__ == '__main__':
    unittest.main()
